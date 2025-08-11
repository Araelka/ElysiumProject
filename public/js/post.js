document.addEventListener('DOMContentLoaded', function () {
    const pusherKey = 'e9b501d88e4c02269a2c'; 
    const pusherCluster = 'ap1'; 

    const postText = document.getElementById('post-text');
    const submitButton = document.getElementById('submit-post');

    const textarea = postText;

    const pusher = new Pusher(pusherKey, {
        cluster: pusherCluster,
        forceTLS: false, 
    });

    const channel = pusher.subscribe('posts');

    channel.bind('App\\Events\\PostEvent', function (data) {        
        const { action, postData } = data;

        if (action === 'create') {
            addPostToDOM(postData);
        }else if (action === 'edit') {
            updatePostInDOM(postData);
        }else if (action === 'delete') {
            deletePostInDOM(postData);
        }
    });

    postText.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault(); 
            submitPostForm(submitButton); 
        }
    });

    textarea.addEventListener('input', autoResize);
    autoResize();

    document.addEventListener('click', function (event) {
        const dropdownCharacterMenu = document.getElementById('character-dropdown');
        const customDropdown = document.querySelector('.custom-dropdown');

        if (!customDropdown.contains(event.target)) {
            dropdownCharacterMenu.classList.remove('show');
        }
    });

});

function autoResize() {
    const textarea = document.getElementById('post-text');
    textarea.style.height = 'auto'; 
    textarea.style.height = `${textarea.scrollHeight}px`; 
}

function submitPostForm(btn) {
    btn.disabled = true;

    const form = document.getElementById('post-form');
    const formData = new FormData(form);
            

    fetch(form.action, {
        method: form.method,
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('#post-form input[name="_token"]').value,
        },
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Ошибка при отправке.');  
            }
            return response.json();
        })
        .then(data => {
            clearEditPost();
            autoResize();
        })
        .finally(() => {
            btn.disabled = false;
        });
}

function clearEditPost() {
    const parentLink = document.getElementById('parent-link');
    const parentPostIdInput = document.getElementById('parent-post-id');
    const postText = document.getElementById('post-text');
    const postId = document.getElementById('post-id');
    const characterId = document.getElementById('selected-character-id');
    const dropdownToggle = document.getElementById('dropdown-toggle');

    document.getElementById('post-form').action = `/game-room/publish`;

    if (parentLink) {
        parentLink.innerHTML = '';
        parentLink.style.display = 'none'; 
        postText.value = '';
        postId.value = null;
        characterId.value = null;
        dropdownToggle.innerText = 'Выберите персонажа';
        dropdownToggle.disabled = false;
        dropdownToggle.style.cursor = 'pointer';
        
        dropdownToggle.onmouseover = function () {
        dropdownToggle.style.textDecoration = 'underline';
        };

        dropdownToggle.onmouseout = function () {
            dropdownToggle.style.textDecoration = 'none';
        };
    }

    if (parentPostIdInput) {
        parentPostIdInput.value = ''; 
    }
}

function clearParentPost() {
    const parentLink = document.getElementById('parent-link');
    const parentPostIdInput = document.getElementById('parent-post-id');

    if (parentLink) {
        parentLink.innerHTML = '';
        parentLink.style.display = 'none'; 
    }

    if (parentPostIdInput) {
        parentPostIdInput.value = ''; 
    }
}

function canEditPost(postUserId) {
    const currentUserId = parseInt(document.querySelector('meta[name="current-user-id"]').getAttribute('content'));
    return currentUserId === postUserId;
}

function canDeletePost(postUserId) {
    const currentUserId = parseInt(document.querySelector('meta[name="current-user-id"]').getAttribute('content'));
    const isEditor = document.querySelector('meta[name="is-editor"]').getAttribute('content') === 'true';
    return currentUserId === postUserId || isEditor;
}

async function fetchPermissions(id) {
    const response = await fetch(`/game-room/api/posts/${id}/permissions`);    
    const data = await response.json();
    return data;
}


function addPostToDOM(postData) {  
    fetchPermissions(postData.id).then(permissions => {
        const baseUrl = document.querySelector('meta[name="base-url"]').getAttribute('content');
        const postsContainer = document.getElementById('posts-container'); 

        const postElement = document.createElement('div');
        postElement.className = 'post';
        postElement.id = `post-${postData.id}`;
        postElement.dataset.postId = postData.id;

        postElement.innerHTML = `
            <div class="post-header">
                <div style="display: flex; flex-direction: row; align-items: center; gap: 5px;">
                    <div>
                        ${postData.character.avatarPath
                            ? `<img src="${baseUrl}${postData.character.avatarPath}" alt="Аватар персонажа" class="dropdown-avatar" style="height: 35px; width: 35px;">`
                            : postData.character.gender === 'Мужской'
                            ? `<img src="/images/characters/characterMale.jpg" alt="Аватар персонажа" class="dropdown-avatar" style="height: 35px; width: 35px;">`
                            : `<img src="/images/characters/characterFemale.jpg" alt="Аватар персонажа" class="dropdown-avatar" style="height: 35px; width: 35px;">`
                        }
                    </div>
                    <div>
                        <h4 style="padding-left: 5px">${postData.character.firstName} ${postData.character.secondName}</h4>
                    </div>
                </div>
                <div style="display: flex; flex-direction: row; align-items: center;">
                    <div class="custom-dropdown-post">
                        <div>
                            <button type="button" class="dropdown-toggle-post" onclick="toggleDropdownPostMenu(this)">...</button>
                        </div>
                        <div class="dropdown-menu-post">
                            <div class="dropdown-item-post" style="padding: 0px">
                                <a href="javascript:void(0)" data-post-id="${postData.id}" onclick="replyPost(this)" data-close-dropdown="true">
                                    <div style="padding: 5px 10px">Ответить</div>
                                </a>
                            </div>
                            ${permissions.isEditable
                                ? `
                                <div class="dropdown-item-post" style="padding: 0px">
                                    <a href="javascript:void(0)" data-post-id="${postData.id}" onclick="editPost(this)" data-close-dropdown="true">
                                        <div style="padding: 5px 10px">Редактировать</div>
                                    </a>
                                </div>
                                `
                                : ''
                            }
                            ${permissions.isDeletable
                                ? `
                                <div data-post-id="${postData.id}">
                                    <form id="delete-post-form-${postData.id}" action="/gameroom/${postData.id}" method="POST" style="margin: 0px;">
                                        <input type="hidden" name="_token" value="${document.querySelector('#post-form input[name="_token"]').value}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button class="dropdown-item-delete-post" type="button" onclick="confirmDelete(event, ${postData.id})" data-close-dropdown="true">Удалить</button>
                                    </form>
                                </div>
                                `
                                : ''
                            }                      
                        </div>
                    </div>
                </div>
                </div>
                        
                ${postData.parentPost
                    ? `
                    <div class="parent-link">
                        <a href="javascript:void(0)" onclick="scrollToPost(${ postData.parentPost.id })" style="text-decoration: none">
                            <div class="parent-link-content">
                                <div style="color: #f4d03f">
                                    ${postData.parentPost.character.firstName} ${postData.parentPost.character.secondName}
                                </div>
                                <div>
                                    ${ postData.parentPost.content }
                                </div>
                            </div>
                        </a>
                    </div>
                    `
                    : ''
                }

                <p class='post-content'>${postData.content}</p>
                <small>
                    <div style="display: flex; flex-direction: row; justify-content: space-between;">
                        <div class="post-date">
                            ${new Date(postData.created_at).toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit', hour12: false })} ${new Date(postData.created_at).toLocaleDateString('ru-RU')}
                        </div>
                        <div>
                            ${postData.character.userLogin}
                        </div>
                    </div>
                </small>
            `;
                
        postsContainer.prepend(postElement);

    });
}

function addLoadPostToDOM(postData) {             
        const baseUrl = document.querySelector('meta[name="base-url"]').getAttribute('content');
        const postsContainer = document.getElementById('posts-container'); 

        const postElement = document.createElement('div');
        postElement.className = 'post';
        postElement.id = `post-${postData.id}`;
        postElement.dataset.postId = postData.id;

        

        postElement.innerHTML = `
            <div class="post-header">
                <div style="display: flex; flex-direction: row; align-items: center; gap: 5px;">
                    <div>
                        ${postData.character.avatarPath
                            ? `<img src="${baseUrl}${postData.character.avatarPath}" alt="Аватар персонажа" class="dropdown-avatar" style="height: 35px; width: 35px;">`
                            : postData.character.gender === 'Мужской'
                            ? `<img src="/images/characters/characterMale.jpg" alt="Аватар персонажа" class="dropdown-avatar" style="height: 35px; width: 35px;">`
                            : `<img src="/images/characters/characterFemale.jpg" alt="Аватар персонажа" class="dropdown-avatar" style="height: 35px; width: 35px;">`
                        }
                    </div>
                    <div>
                        <h4 style="padding-left: 5px">${postData.character.firstName} ${postData.character.secondName}</h4>
                    </div>
                </div>
                <div style="display: flex; flex-direction: row; align-items: center;">
                    <div class="custom-dropdown-post">
                        <div>
                            <button type="button" class="dropdown-toggle-post" onclick="toggleDropdownPostMenu(this)">...</button>
                        </div>
                        <div class="dropdown-menu-post">
                            <div class="dropdown-item-post" style="padding: 0px">
                                <a href="javascript:void(0)" data-post-id="${postData.id}" onclick="replyPost(this)" data-close-dropdown="true">
                                    <div style="padding: 5px 10px">Ответить</div>
                                </a>
                            </div>
                            ${postData.isEditable
                                ? `
                                <div class="dropdown-item-post" style="padding: 0px">
                                    <a href="javascript:void(0)" data-post-id="${postData.id}" onclick="editPost(this)" data-close-dropdown="true">
                                        <div style="padding: 5px 10px">Редактировать</div>
                                    </a>
                                </div>
                                `
                                : ''
                            }
                            ${postData.isDeletable
                                ? `
                                <div data-post-id="${postData.id}">
                                    <form id="delete-post-form-${postData.id}" action="/gameroom/${postData.id}" method="POST" style="margin: 0px;">
                                        <input type="hidden" name="_token" value="${document.querySelector('#post-form input[name="_token"]').value}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button class="dropdown-item-delete-post" type="button" onclick="confirmDelete(event, ${postData.id})" data-close-dropdown="true">Удалить</button>
                                    </form>
                                </div>
                                `
                                : ''
                            }                      
                        </div>
                    </div>
                </div>
                </div>
                        
                ${postData.parentPost
                    ? `
                    <div class="parent-link">
                        <a href="javascript:void(0)" onclick="scrollToPost(${ postData.parentPost.id })" style="text-decoration: none">
                            <div class="parent-link-content">
                                <div style="color: #f4d03f">
                                    ${postData.parentPost.character.firstName} ${postData.parentPost.character.secondName}
                                </div>
                                <div>
                                    ${ postData.parentPost.content }
                                </div>
                            </div>
                        </a>
                    </div>
                    `
                    : ''
                }

                <p class='post-content'>${postData.content}</p>
                <small>
                    <div style="display: flex; flex-direction: row; justify-content: space-between;">
                        <div class="post-date">
                            ${new Date(postData.created_at).toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit', hour12: false })} ${new Date(postData.created_at).toLocaleDateString('ru-RU')}
                        </div>
                        <div>
                            ${postData.character.userLogin}
                        </div>
                    </div>
                </small>
            `;
                
        postsContainer.append(postElement);

}

function updatePostInDOM(postData) {

    const postElement = document.getElementById(`post-${postData.id}`);
    
    if (postElement) {
        const postElementContent = postElement.getElementsByClassName(`post-content`)[0];
        const postElementDate = postElement.getElementsByClassName(`post-date`)[0];
        
        postElementContent.innerHTML = postData.content;
        postElementDate.innerHTML = new Date(postData.updated_at).toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit', hour12: false }) + ' ' + new Date(postData.updated_at).toLocaleDateString('ru-RU') + ' (изм)';
        
    }
}

function deletePostInDOM(postData) {

    console.log(postData);
    

    const postElement = document.getElementById(`post-${postData.id}`);
            
    if (postElement) {
        postElement.remove();
    }
}

function editPost(button) {
    const postId = button.getAttribute('data-post-id');

    fetch(`/game-room/get-post-content/${postId}`)
        .then(response => response.json())
        .then(data => {                
            document.getElementById('post-id').value = postId; 
            document.getElementById('selected-character-id').value = data.character_uuid;
            document.querySelector('.dropdown-toggle').innerText = `${data.character_name}`; 
            document.getElementById('post-text').value = data.content; 
            
            const dropdownToggle = document.getElementById('dropdown-toggle');
            dropdownToggle.disabled = true;
            dropdownToggle.style.cursor = 'auto';
            dropdownToggle.onmouseover = function () {
            dropdownToggle.style.textDecoration = 'none';
            };

            dropdownToggle.onmouseout = function () {
                dropdownToggle.style.textDecoration = 'none';
            };

            const parentLink = document.getElementById('parent-link');
                parentLink.innerHTML = `
                    <div style="display: flex; flex-direction: row; justify-content: space-between; align-items: flex-start;">
                        <a href="javascript:void(0)" onclick="scrollToPost(${data.id})" style="text-decoration: none">
                            <div class="parent-link-content">
                                <div style="color: #f4d03f">${data.character_name}</div>
                                <div>${data.content}</div>
                            </div>
                        </a>
                        <div class="parent-post-close" onclick="clearEditPost()">&#10006;</div>
                    </div>
                `;
                parentLink.style.display = 'block';

            document.getElementById('post-form').action = `/game-room/edit/${postId}`;
        })
        .catch(error => console.error('Ошибка:', error));
}

function replyPost(button) {
    const postId = button.getAttribute('data-post-id'); 
    const parentPostIdInput = document.getElementById('parent-post-id'); 
    const parentLink = document.getElementById('parent-link');
    const dropdownToggle = document.getElementById('dropdown-toggle');
    
    document.getElementById('post-form').action = `/game-room/publish`;
    parentPostIdInput.value = postId;
    dropdownToggle.disabled = false;
    dropdownToggle.style.cursor = 'pointer';
    dropdownToggle.onmouseover = function () {
    dropdownToggle.style.textDecoration = 'underline';
    };

    dropdownToggle.onmouseout = function () {
        dropdownToggle.style.textDecoration = 'none';
    };

    if (postId) {
        fetch(`/game-room/get-post-content/${postId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Сообщение не найдено');
                }
                return response.json();
            })
            .then(data => {
                if (parentLink) {
                    parentLink.innerHTML = '';

                    parentLink.innerHTML = `
                    <div style="display: flex; flex-direction: row; justify-content: space-between; align-items: flex-start;">
                        <a href="javascript:void(0)" onclick="scrollToPost(${postId})" style="text-decoration: none">
                            <div class="parent-link-content">
                                <div style="color: #f4d03f">${data.character_name}</div>
                                <div>${data.content}</div>
                            </div>
                        </a>
                        <div class="parent-post-close" onclick="clearParentPost()">&#10006;</div>
                    </div>
                    `;
                    parentLink.style.display = 'block'; 
                }
            })
            .catch(error => console.error('Ошибка:', error));
    } else {
        if (parentLink) {
            parentLink.style.display = 'none';
        }
    }
}

function scrollToPost(postId) {
    const postsContainer = document.getElementById('posts-container'); 
    const postElement = document.querySelector(`#post-${postId}`);

    if (postElement && postsContainer) {
            
            const postTop = postElement.offsetTop - postsContainer.offsetTop;

            postsContainer.scrollTo({
                top: postTop,
                behavior: 'smooth' 
            });

            postElement.style.backgroundColor = '#f4d03f20'; 
            setTimeout(() => {
                postElement.style.backgroundColor = ''; 
            }, 2000);
        }
}