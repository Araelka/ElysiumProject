document.addEventListener('DOMContentLoaded', function () {
    const pusherKey = 'e9b501d88e4c02269a2c'; 
    const pusherCluster = 'ap1'; 

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
});


function canEditPost(postUserId) {
    const currentUserId = parseInt(document.querySelector('meta[name="current-user-id"]').getAttribute('content'));
    return currentUserId === postUserId;
}

function canDeletePost(postUserId) {
    const currentUserId = parseInt(document.querySelector('meta[name="current-user-id"]').getAttribute('content'));
    const isEditor = document.querySelector('meta[name="is-editor"]').getAttribute('content') === 'true';
    return currentUserId === postUserId || isEditor;
}


function addPostToDOM(postData) {  
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
                        ${canEditPost(postData.character.userId)
                            ? `
                            <div class="dropdown-item-post" style="padding: 0px">
                                <a href="javascript:void(0)" data-post-id="${postData.id}" onclick="editPost(this)" data-close-dropdown="true">
                                    <div style="padding: 5px 10px">Редактировать</div>
                                </a>
                            </div>
                            `
                            : ''
                        }
                        ${canDeletePost(postData.character.userId)
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