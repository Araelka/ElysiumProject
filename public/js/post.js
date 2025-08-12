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

function getBaseUrl() {
    if (!cachedBaseUrl) {
        const metaTag = document.querySelector('meta[name="base-url"]');
        cachedBaseUrl = metaTag ? metaTag.getAttribute('content') : '/';
    }
    return cachedBaseUrl;
}

function getCsrfToken() {
    if (!cachedToken) {
        const tokenInput = document.querySelector('#post-form input[name="_token"]');
        cachedToken = tokenInput ? tokenInput.value : '';
    }
    return cachedToken;
}

const permissionsCache = new Map();
const pendingRequests = new Map();
let cachedBaseUrl = null;
let cachedToken = null;

async function fetchPermissions(id) {
    if (permissionsCache.has(id.toString())) { 
        return permissionsCache.get(id.toString());
    }

    if (pendingRequests.has(id.toString())) {
        return pendingRequests.get(id.toString());
    }

    const requestPromise = fetch(`/game-room/api/posts/${id}/permissions`)
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            permissionsCache.set(id.toString(), data);
            pendingRequests.delete(id.toString());
            return data;
        })
        .catch(error => {
            pendingRequests.delete(id.toString());
            console.error(`Ошибка загрузки разрешений для поста ${id}:`, error);
            return { isEditable: false, isDeletable: false };
        });

    pendingRequests.set(id.toString(), requestPromise);
    return requestPromise;
}

function createPostElement(postData, permissions, baseUrl, csrfToken) {
    const postElement = document.createElement('div');
    postElement.className = 'post';
    postElement.id = `post-${postData.id}`;
    postElement.dataset.postId = postData.id;

    let avatarHtml = '';
    if (postData.character.avatarPath) {
        avatarHtml = `<img src="${baseUrl}${postData.character.avatarPath}" alt="Аватар персонажа" class="dropdown-avatar" style="height: 35px; width: 35px;">`;
    } else if (postData.character.gender === 'Мужской') {
        avatarHtml = `<img src="/images/characters/characterMale.jpg" alt="Аватар персонажа" class="dropdown-avatar" style="height: 35px; width: 35px;">`;
    } else {
        avatarHtml = `<img src="/images/characters/characterFemale.jpg" alt="Аватар персонажа" class="dropdown-avatar" style="height: 35px; width: 35px;">`;
    }

    let parentPostHtml = '';
    if (postData.parentPost) {
        parentPostHtml = `
            <div class="parent-link">
                <a href="javascript:void(0)" onclick="scrollToPost(${postData.parentPost.id})" style="text-decoration: none">
                    <div class="parent-link-content">
                        <div style="color: #f4d03f">
                            ${escapeHtml(postData.parentPost.character.firstName)} ${escapeHtml(postData.parentPost.character.secondName)}
                        </div>
                        <div>
                            ${escapeHtml(postData.parentPost.content)}
                        </div>
                    </div>
                </a>
            </div>
        `;
    }

    const isEditable = postData.isEditable !== undefined ? postData.isEditable : permissions?.isEditable;
    const isDeletable = postData.isDeletable !== undefined ? postData.isDeletable : permissions?.isDeletable;
    const isModerator = postData.isModerator !== undefined ? postData.isModerator : false;
    const diffInHours = postData.diffInHours !== undefined ? postData.diffInHours : 0;

    const showEdit = (isEditable && diffInHours <= 24);
    const showDelete = ((isDeletable && diffInHours <= 24) || isModerator);

    let dropdownMenuHtml = `
        <div class="dropdown-item-post" style="padding: 0px">
            <a href="javascript:void(0)" data-post-id="${postData.id}" onclick="replyPost(this)" data-close-dropdown="true">
                <div style="padding: 5px 10px">Ответить</div>
            </a>
        </div>
    `;

    if (showEdit) {
        dropdownMenuHtml += `
            <div class="dropdown-item-post" style="padding: 0px">
                <a href="javascript:void(0)" data-post-id="${postData.id}" onclick="editPost(this)" data-close-dropdown="true">
                    <div style="padding: 5px 10px">Редактировать</div>
                </a>
            </div>
        `;
    }

    if (showDelete) {
        dropdownMenuHtml += `
            <div data-post-id="${postData.id}">
                <form id="delete-post-form-${postData.id}" action="/gameroom/${postData.id}" method="POST" style="margin: 0px;">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button class="dropdown-item-delete-post" type="button" onclick="confirmDelete(event, ${postData.id})" data-close-dropdown="true">Удалить</button>
                </form>
            </div>
        `;
    }

    let dateHtml = '';
    if (postData.created_at != postData.updated_at) {
       dateHtml = `${postData.updated_at} (изм)`;
    } else {
       dateHtml = `${postData.created_at}`;
    }

    postElement.innerHTML = `
        <div class="post-header">
            <div style="display: flex; flex-direction: row; align-items: center; gap: 5px;">
                <div>${avatarHtml}</div>
                <div>
                    <h4 style="padding-left: 5px">${escapeHtml(postData.character.firstName)} ${escapeHtml(postData.character.secondName)}</h4>
                </div>
            </div>
            <div style="display: flex; flex-direction: row; align-items: center;">
                <div class="custom-dropdown-post">
                    <div>
                        <button type="button" class="dropdown-toggle-post" onclick="toggleDropdownPostMenu(this)">...</button>
                    </div>
                    <div class="dropdown-menu-post">
                        ${dropdownMenuHtml}
                    </div>
                </div>
            </div>
        </div>
        ${parentPostHtml}
        <p class='post-content'>${escapeHtml(postData.content)}</p>
        <small>
            <div style="display: flex; flex-direction: row; justify-content: space-between;">
                <div class="post-date">
                    ${dateHtml}
                </div>
                <div>
                    ${escapeHtml(postData.character.userLogin)}
                </div>
            </div>
        </small>
    `;
    return postElement;
}

function escapeHtml(text) {
  const map = {
    '&': '&amp;',
    '<': '<',
    '>': '>',
    '"': '&quot;',
    "'": '&#039;'
  };

  return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

let isInitialLoad = true;

async function addPostToDOM(postData) {
    const postsContainer = document.getElementById('posts-container');
    if (!postsContainer) return;

    try {
        if (postsContainer.children.length === 1) {
            const firstChild = postsContainer.children[0];
            if (firstChild.classList.contains('no-results') || firstChild.classList.contains('loading')) {
                postsContainer.innerHTML = ''; 
            }
        }

        const permissions = await fetchPermissions(postData.id);

        const baseUrl = getBaseUrl();
        const csrfToken = getCsrfToken();

        const postElement = createPostElement(postData, permissions, baseUrl, csrfToken);

        postsContainer.insertAdjacentElement('afterbegin', postElement);

        if (isInitialLoad || postsContainer.scrollTop >= -150) { 
             postsContainer.scrollTo({
                 top: 0, 
                 behavior: 'smooth'
             });
        }
        isInitialLoad = false; 

    } catch (error) {
        console.error('Ошибка при добавлении нового поста:', error);
    }
}

async function addPostsToDOMBatch(postsData) {
    const postsContainer = document.getElementById('posts-container');
    if (!postsContainer || !postsData || postsData.length === 0) return;

    try {
        const baseUrl = getBaseUrl();
        const csrfToken = getCsrfToken();

        const fragment = document.createDocumentFragment();

        for (const postData of postsData) {
            const permissions = {
                isEditable: postData.isEditable,
                isDeletable: postData.isDeletable
            };
            const postElement = createPostElement(postData, permissions, baseUrl, csrfToken);
            if (postElement) {
                fragment.appendChild(postElement);
            }
        }

        postsContainer.appendChild(fragment);

    } catch (error) {
        console.error('Ошибка при batch-добавлении постов:', error);
    }
}


function updatePostInDOM(postData) {

    const postElement = document.getElementById(`post-${postData.id}`);
    
    if (postElement) {
        const postElementContent = postElement.getElementsByClassName(`post-content`)[0];
        const postElementDate = postElement.getElementsByClassName(`post-date`)[0];
        
        postElementContent.innerHTML = postData.content;
        postElementDate.innerHTML = postData.updated_at + ' (изм)';
        
    }
}

function deletePostInDOM(postData) {
    
    const postElement = document.getElementById(`post-${postData.id}`);
        
    if (postElement) {
        postElement.remove();
    }

    if (postData.replay) {
        postData.replay.forEach(post => {
            const parentPostElement = document.getElementById(`post-${post.id}`);
            parentPostElement.getElementsByClassName('parent-link')[0].innerHTML = ''
        });
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
