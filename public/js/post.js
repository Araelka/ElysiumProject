let currentPage = 1;
let isLoading = false;
let hasMorePosts = true;
let isInitialLoad = true;
let firstUnreadPostIdOnLoad = null;
let globalPostsContainer = null;
let currentSearchQuery = null;
let unreadPostsTrackingInitialized = false;
let visibilityCheckScheduled = false;
let scrollTimer = null;
let cachedBaseUrl = null;
let cachedToken = null;
const permissionsCache = new Map();
const pendingRequests = new Map();
let currentPostId = null;
let currentUnreadSeparator = null;

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

    channel.bind('PostEvent', function (data) {
        const { action, postData } = data;

        if (action === 'create') {
            addPostToDOM(postData);
            fetchUnreadCounts();
        } else if (action === 'edit') {
            updatePostInDOM(postData);
        } else if (action === 'delete') {
            deletePostInDOM(postData);
            fetchUnreadCounts();
        }
    });

    channel.bind('PostRead', function (data) {
        const { postId, readerUserId, readerCharacterName } = data;
        const currentUserId = window.currentUserId;    
            

        if (currentUserId && parseInt(readerUserId, 10) !== parseInt(currentUserId, 10)) {
            const postElement = document.getElementById(`post-${postId}`);

            if (postElement) {
                const readIndicatorsContainer = postElement.querySelector('.read-post');

                if (readIndicatorsContainer) {
                    let readByOthersIndicator = readIndicatorsContainer.querySelector('.read-by-others');
                    if (!readByOthersIndicator) {
                        readByOthersIndicator = document.createElement('div');
                        readByOthersIndicator.className = 'read-indicator read-by-others';
                        readByOthersIndicator.title = `Прочитано другими`;
                        readByOthersIndicator.innerHTML = '&#10003;';
                        readIndicatorsContainer.appendChild(readByOthersIndicator); 
                    }
                }
            }
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

    const postsContainer = document.getElementById('posts-container');
    globalPostsContainer = postsContainer;

    if (postsContainer) {
        postsContainer.scrollTop = 0;

        function isScrollNearTop(container) {
            return container.scrollHeight + container.scrollTop - container.clientHeight <= 300;
        }

        postsContainer.addEventListener('scroll', () => {
            if (isScrollNearTop(postsContainer)) {
                loadPosts();
            }
        });

        const buttonBottom = document.getElementById('button-bottom');
        if (buttonBottom) {
            postsContainer.addEventListener('scroll', () => {
                if (postsContainer.scrollTop <= -100) {
                    buttonBottom.style.display = 'block';
                } else {
                    buttonBottom.style.display = 'none';
                }
            });
        }
    }

    loadPosts();
    fetchUnreadCounts();

    setTimeout(initUnreadPostsTracking, 1000);

    const searchForm = document.getElementById('search-form');
    const searchInput = document.getElementById('search-input');
    const clearSearchButton = document.getElementById('clear-search');

    if (searchForm) {
        searchForm.addEventListener('submit', function (event) {
            event.preventDefault();
            if (clearSearchButton) clearSearchButton.style.display = 'block';

            if (searchInput) {
                const searchTerm = searchInput.value;
                performSearch(searchTerm);
            }
        });
    }

    if (clearSearchButton && searchInput) {
        clearSearchButton.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopImmediatePropagation();
            clearSearchButton.style.display = 'none';
            searchInput.value = '';
            performSearch('');
        });
    }
});


function autoResize() {
    const textarea = document.getElementById('post-text');
    if (textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = `${textarea.scrollHeight}px`;
    }
}

function submitPostForm(btn) {
    if (btn) btn.disabled = true;

    const form = document.getElementById('post-form');
    if (!form) return;

    const formData = new FormData(form);

    fetch(form.action, {
        method: form.method,
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('#post-form input[name="_token"]')?.value,
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
        .catch(error => {
            console.error('Ошибка при отправке формы:', error);
        })
        .finally(() => {
            if (btn) btn.disabled = false;
        });
}

function clearEditPost() {
    const parentLink = document.getElementById('parent-link');
    const parentPostIdInput = document.getElementById('parent-post-id');
    const postText = document.getElementById('post-text');
    const postId = document.getElementById('post-id');
    const characterId = document.getElementById('selected-character-id');
    const dropdownToggle = document.getElementById('dropdown-toggle');
    const postForm = document.getElementById('post-form');

    if (postForm) postForm.action = `/game-room/publish`;

    if (parentLink) {
        parentLink.innerHTML = '';
        parentLink.style.display = 'none';
    }

    if (postText) postText.value = '';
    if (postId) postId.value = null;
    if (characterId) characterId.value = null;

    if (dropdownToggle) {
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

    if (parentPostIdInput) parentPostIdInput.value = '';
}

function clearParentPost() {
    const parentLink = document.getElementById('parent-link');
    const parentPostIdInput = document.getElementById('parent-post-id');

    if (parentLink) {
        parentLink.innerHTML = '';
        parentLink.style.display = 'none';
    }

    if (parentPostIdInput) parentPostIdInput.value = '';
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

async function fetchPermissions(id) {
    const idStr = id.toString();
    if (permissionsCache.has(idStr)) {
        return permissionsCache.get(idStr);
    }

    if (pendingRequests.has(idStr)) {
        return pendingRequests.get(idStr);
    }

    const requestPromise = fetch(`/game-room/api/posts/${id}/permissions`)
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            permissionsCache.set(idStr, data);
            pendingRequests.delete(idStr);
            return data;
        })
        .catch(error => {
            pendingRequests.delete(idStr);
            console.error(`Ошибка загрузки разрешений для поста ${id}:`, error);
            return { isEditable: false, isDeletable: false };
        });

    pendingRequests.set(idStr, requestPromise);
    return requestPromise;
}

function createPostElement(postData, permissions, baseUrl, csrfToken) {
    const postElement = document.createElement('div');
    postElement.className = 'post';
    
    if (!postData.isRead) {
        postElement.classList.add('post-unread');
    }

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

    let readIndicatorsHtml = '';

    if (postData.isReadByOthers) {
        readIndicatorsHtml += `<div class="read-indicator read-by-others" title="Прочитано другими">&#10003;</div>`;
    }

    if (postData.isRead) {
        readIndicatorsHtml += `<div class="read-indicator read-by-me" title="Прочитано вами">&#10003;</div>`;
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
                <div class="read-post" style="display: flex; margin-right: 10px;"> 
                    ${readIndicatorsHtml}
                </div>
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

    return text.replace(/[&<>"']/g, function (m) {
        return map[m];
    });
}

async function addPostToDOM(postData) {
    const postsContainer = document.getElementById('posts-container');
    if (!postsContainer) return;


    const currentLocId = window.currentLocationId;
    const postLocId = postData.location_id;

    const isCurrentLocDefined = (currentLocId !== null && currentLocId !== undefined && currentLocId !== '' && !isNaN(currentLocId));
    const isPostLocDefined = (postLocId !== null && postLocId !== undefined && postLocId !== '' && !isNaN(postLocId));

    if (!isCurrentLocDefined || !isPostLocDefined || parseInt(currentLocId, 10) !== parseInt(postLocId, 10)) {
        return;
    }

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
        // markPostAsRead(postData.id);
        
        postsContainer.insertAdjacentElement('afterbegin', postElement);
        
        if (!isInitialLoad && postsContainer.scrollTop <= -150) {
            if (postData.character.userId != currentUserId) {
                if (!currentUnreadSeparator) {
                    currentUnreadSeparator = document.createElement('div');
                    currentUnreadSeparator.className = 'unread-posts-separator';
                    currentUnreadSeparator.innerHTML = '<span>Непрочитанные сообщения</span>';
                    postsContainer.insertBefore(currentUnreadSeparator, postElement.nextSibling); 
                    console.log("Создан новый разделитель для новых непрочитанных постов.");
                }
            }
        }

        if (isInitialLoad || postsContainer.scrollTop >= -150) {
            postsContainer.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
        isInitialLoad = false;

        setTimeout(() => {
            checkUnreadPostsVisibility().catch(err => console.error("Ошибка в checkUnreadPostsVisibility из addPostToDOM:", err));
        }, 30);

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

        let separatorAdded = false;

        const shouldAddSeparator = isInitialLoad && firstUnreadPostIdOnLoad;
        

        for (let i = postsData.length - 1; i >= 0; i--) {
            const postData = postsData[i];
            if (shouldAddSeparator && postData.id == firstUnreadPostIdOnLoad && !postData.isRead) {
                if (!separatorAdded) {
                    const separator = document.createElement('div');
                    separator.className = 'unread-posts-separator';
                    separator.innerHTML = '<span>Непрочитанные сообщения</span>';
                    fragment.prepend(separator);
                    separatorAdded = true;
                    currentUnreadSeparator = separator;
                }
            }

            const permissions = {
                isEditable: postData.isEditable,
                isDeletable: postData.isDeletable
            };
            const postElement = createPostElement(postData, permissions, baseUrl, csrfToken);
            if (postElement) {
                fragment.prepend(postElement);
            }
        }


        postsContainer.appendChild(fragment);

        if (isInitialLoad) {
            setTimeout(() => {
                let targetScrollTop = postsContainer.scrollHeight; 
                let targetElement = null;
                let logMessage = "Прокрутка вниз по умолчанию.";

                if (firstUnreadPostIdOnLoad) {
                    const unreadPostElement = document.getElementById(`post-${firstUnreadPostIdOnLoad}`);
                    if (unreadPostElement) {
                        let separatorElement = null;
                        let previousElement = unreadPostElement.previousElementSibling;
                        while (previousElement) {
                            if (previousElement.classList.contains('unread-posts-separator')) {
                                separatorElement = previousElement;
                                break;
                            }
                            previousElement = previousElement.previousElementSibling;
                        }

                        targetElement = separatorElement || unreadPostElement;
                        targetScrollTop = targetElement.offsetTop;
                    } 
                } 

                
                postsContainer.scrollTo({
                    top: targetScrollTop - postsContainer.offsetTop - 500,
                    behavior: 'instant' 
                });

                isInitialLoad = false; 
                initUnreadPostsTracking();
            }, 100);

        } else {
            isInitialLoad = false;
            if (!unreadPostsTrackingInitialized) {
                setTimeout(() => {
                    initUnreadPostsTracking();
                }, 100);
            }
            setTimeout(() => {
                checkUnreadPostsVisibility().catch(err => console.error("Ошибка в checkUnreadPostsVisibility из addPostsToDOMBatch:", err));
            }, 50);
        }

    } catch (error) {
        console.error('Ошибка при batch-добавлении постов:', error);
        if (isInitialLoad) {
             isInitialLoad = false;
             setTimeout(() => {
                 initUnreadPostsTracking();
             }, 100);
        }
    }
}

function updatePostInDOM(postData) {
    const postElement = document.getElementById(`post-${postData.id}`);
    if (postElement) {
        const postElementContent = postElement.querySelector('.post-content');
        const postElementDate = postElement.querySelector('.post-date');

        if (postElementContent) postElementContent.innerHTML = postData.content;
        if (postElementDate) postElementDate.innerHTML = postData.updated_at + ' (изм)';
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
            if (parentPostElement) {
                const parentLink = parentPostElement.querySelector('.parent-link');
                if (parentLink) parentLink.innerHTML = '';
            }
        });
    }
}

function editPost(button) {
    const postId = button.getAttribute('data-post-id');
    if (!postId) return;

    fetch(`/game-room/get-post-content/${postId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Сообщение не найдено');
            }
            return response.json();
        })
        .then(data => {
            const postIdInput = document.getElementById('post-id');
            const characterIdInput = document.getElementById('selected-character-id');
            const dropdownToggle = document.getElementById('dropdown-toggle');
            const postText = document.getElementById('post-text');
            const parentLink = document.getElementById('parent-link');
            const postForm = document.getElementById('post-form');

            if (postIdInput) postIdInput.value = postId;
            if (characterIdInput) characterIdInput.value = data.character_uuid;
            if (dropdownToggle) dropdownToggle.innerText = `${data.character_name}`;
            if (postText) postText.value = data.content;

            if (dropdownToggle) {
                dropdownToggle.disabled = true;
                dropdownToggle.style.cursor = 'auto';
                dropdownToggle.onmouseover = function () {
                    dropdownToggle.style.textDecoration = 'none';
                };
                dropdownToggle.onmouseout = function () {
                    dropdownToggle.style.textDecoration = 'none';
                };
            }

            if (parentLink) {
                parentLink.innerHTML = `
                    <div style="display: flex; flex-direction: row; justify-content: space-between; align-items: flex-start;">
                        <a href="javascript:void(0)" onclick="scrollToPost(${data.id})" style="text-decoration: none">
                            <div class="parent-link-content">
                                <div style="color: #f4d03f">${escapeHtml(data.character_name)}</div>
                                <div>${escapeHtml(data.content)}</div>
                            </div>
                        </a>
                        <div class="parent-post-close" onclick="clearEditPost()">&#10006;</div>
                    </div>
                `;
                parentLink.style.display = 'block';
            }

            if (postForm) postForm.action = `/game-room/edit/${postId}`;
        })
        .catch(error => console.error('Ошибка при редактировании поста:', error));
}

function replyPost(button) {
    const postId = button.getAttribute('data-post-id');
    const parentPostIdInput = document.getElementById('parent-post-id');
    const parentLink = document.getElementById('parent-link');
    const dropdownToggle = document.getElementById('dropdown-toggle');
    const postForm = document.getElementById('post-form');

    if (postForm) postForm.action = `/game-room/publish`;
    if (parentPostIdInput) parentPostIdInput.value = postId || '';

    if (dropdownToggle) {
        dropdownToggle.disabled = false;
        dropdownToggle.style.cursor = 'pointer';
        dropdownToggle.onmouseover = function () {
            dropdownToggle.style.textDecoration = 'underline';
        };
        dropdownToggle.onmouseout = function () {
            dropdownToggle.style.textDecoration = 'none';
        };
    }

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
                                <div style="color: #f4d03f">${escapeHtml(data.character_name)}</div>
                                <div>${escapeHtml(data.content)}</div>
                            </div>
                        </a>
                        <div class="parent-post-close" onclick="clearParentPost()">&#10006;</div>
                    </div>
                    `;
                    parentLink.style.display = 'block';
                }
            })
            .catch(error => console.error('Ошибка при ответе на пост:', error));
    } else {
        if (parentLink) {
            parentLink.style.display = 'none';
        }
    }
}



function isElementInViewport(el, container) {
    const rect = el.getBoundingClientRect();
    const containerRect = container.getBoundingClientRect();

    const threshold = 50;
    return (
        rect.bottom >= containerRect.top + threshold &&
        rect.top <= containerRect.bottom - threshold
    );
}

async function markPostAsRead(postId) {
    const csrfToken = getCsrfToken();
    if (!csrfToken) {
        console.warn('CSRF токен не найден');
        return false;
    }

    try {
        const response = await fetch(`/game-room/posts/${postId}/mark-as-read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
        });

        if (!response.ok) {
            console.warn(`Ошибка при отметке поста ${postId} как прочитанного:`, response.status);
            return false;
        }

        const data = await response.json();
        if (data.success) {
            console.log(`Пост ${postId} отмечен как прочитанный.`);
            return true;
        } else {
            console.warn(`Ошибка при отметке поста ${postId} как прочитанного (сервер):`, data);
            return false;
        }
    } catch (error) {
        console.error(`Ошибка сети при отметке поста ${postId} как прочитанного:`, error);
        return false;
    }
}

async function checkUnreadPostsVisibility() {
    const postsContainer = globalPostsContainer || document.getElementById('posts-container');
    if (!postsContainer) return;

    const unreadPosts = postsContainer.querySelectorAll('.post-unread');
    const markAsReadPromises = [];

    for (const post of unreadPosts) {
        const postId = post.dataset.postId;
        if (postId && isElementInViewport(post, postsContainer)) {
            markAsReadPromises.push({ postId, postElement: post, promise: markPostAsRead(postId) });
        }
    }

    if (markAsReadPromises.length > 0) {
        try {
            const results = await Promise.all(markAsReadPromises.map(item => item.promise));

            let anyMarkedAsRead = false;
            for (let i = 0; i < results.length; i++) {
                const { postId, postElement } = markAsReadPromises[i];
                const success = results[i];
                if (success) {
                    const readIndicatorsContainer = postElement.querySelector('.read-post');
                    if (readIndicatorsContainer) {
                        let readByMeIndicator = readIndicatorsContainer.querySelector('.read-by-me');
                        if (!readByMeIndicator){
                            readByMeIndicator = document.createElement('div');
                            readByMeIndicator.className = 'read-indicator read-by-me';
                            readByMeIndicator.title = 'Прочитано вами';
                            readByMeIndicator.innerHTML = '&#10003;';
                            readIndicatorsContainer.insertBefore(readByMeIndicator, readIndicatorsContainer.firstChild);
                        }
                    }
                    postElement.classList.remove('post-unread');
                    anyMarkedAsRead = true;
                }
            }
            if (anyMarkedAsRead) {
                fetchUnreadCounts();
            }
        } catch (error) {
            console.error("Ошибка при обработке результатов отметки постов как прочитанных:", error);
        }
    }
}

function initUnreadPostsTracking() {
    if (unreadPostsTrackingInitialized) return;
    unreadPostsTrackingInitialized = true;

    const postsContainer = document.getElementById('posts-container');
    globalPostsContainer = postsContainer;
    if (!postsContainer) return;

    let throttleTimer;
    postsContainer.addEventListener('scroll', () => {        
        clearTimeout(throttleTimer);
        throttleTimer = setTimeout(() => {
            const isAtBottom = Math.abs(postsContainer.scrollTop) < 150;

            if (isAtBottom && currentUnreadSeparator) {
                currentUnreadSeparator.remove();
                currentUnreadSeparator = null;
            }

            checkUnreadPostsVisibility();
        }, 100);
    });


    setTimeout(checkUnreadPostsVisibility, 500);
}



function updateLocationUnreadCounts(countsData) {
    for (const [locIdStr, count] of Object.entries(countsData)) {
        const locId = parseInt(locIdStr, 10);
        const link = document.querySelector(`.topic-link[href*="location_id=${locId}"]`);
        if (link) {
            let badge = link.querySelector('.unread-count-badge');
            if (count > 0) {
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'unread-count-badge';
                    link.appendChild(badge);
                }
                if (badge) badge.textContent = count;
            } else if (badge) {
                badge.remove();
            }
        }
    }
}

async function fetchUnreadCounts() {
    const locationLinks = document.querySelectorAll('.topic-link[href*="location_id="]');
    const locationIds = Array.from(locationLinks).map(link => {
        const match = link.href.match(/location_id=(\d+)/);
        return match ? parseInt(match[1], 10) : null;
    }).filter(id => id !== null);

    if (locationIds.length === 0) return;

    try {
        const response = await fetch(`/game-room/unread-counts?location_ids[]=${locationIds.join('&location_ids[]=')}`);

        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        const data = await response.json();

        if (data.counts) {
            updateLocationUnreadCounts(data.counts);
        }
    } catch (error) {
        console.error("Ошибка при получении счетчиков непрочитанных:", error);
    }
}



async function loadPosts() {
    if (isLoading || (!hasMorePosts && !isInitialLoad)) return;

    isLoading = true;
    const postsContainer = document.getElementById('posts-container');
    const loadingIndicator = document.getElementById('loading-indicator') || document.createElement('div');

    if (!loadingIndicator.id) {
        loadingIndicator.id = 'loading-indicator';
        loadingIndicator.className = 'loading';
        loadingIndicator.textContent = 'Загрузка...';
    }

    const locationId = window.currentLocationId;

    try {
        if (!locationId) {
            throw new Error('Локация не выбрана');
        }

        let url = `/game-room/load-posts?location_id=${locationId}&page=${currentPage}`;

        if (currentSearchQuery) {
            url += `&search=${encodeURIComponent(currentSearchQuery)}`;
        }

        if ((currentPage > 1 || currentSearchQuery) && postsContainer) {
            postsContainer.appendChild(loadingIndicator);
        } else if (isInitialLoad && postsContainer) {
            postsContainer.innerHTML = '<div class="loading">Загрузка постов...</div>';
        }

        const response = await fetch(url);
        if (!response.ok) {
            if (response.status === 403) {
                throw new Error('Доступ запрещен.');
            } else if (response.status === 400) {
                throw new Error('Неверный запрос.');
            } else {
                throw new Error(`Ошибка сети: ${response.status}`);
            }
        }

        const data = await response.json();

        if (currentPage === 1 && !currentSearchQuery) {
            firstUnreadPostIdOnLoad = data.firstUnreadPostId || null;
        }

        if (data.posts && data.posts.length > 0) {
            if (currentPage === 1 && postsContainer) {
                postsContainer.innerHTML = '';
            }

            await addPostsToDOMBatch(data.posts);
            
            currentPage++;
            
            hasMorePosts = data.hasMore;

        } else if (data.posts && data.posts.length === 0 && currentPage === 1) {
            if (postsContainer) {
                postsContainer.innerHTML = currentSearchQuery ?
                    '<div class="no-results">По вашему запросу ничего не найдено.</div>' :
                    '<div class="no-results">Постов пока нет.</div>';
            }
            hasMorePosts = false;
            isInitialLoad = false; 
            initUnreadPostsTracking(); 
        }
    } catch (error) {
        console.error('Ошибка загрузки постов:', error);
        if (currentPage === 1 && postsContainer) {
            postsContainer.innerHTML = `<div class="error-loading">Ошибка загрузки постов: ${error.message}</div>`;
        }
        hasMorePosts = false;
        isInitialLoad = false; 
        initUnreadPostsTracking(); 
    } finally {
        if (loadingIndicator.parentNode) {
            loadingIndicator.parentNode.removeChild(loadingIndicator);
        }
        isLoading = false;
    }
}

function performSearch(query) {
    const trimmedQuery = query.trim();
    currentSearchQuery = trimmedQuery.length > 0 ? trimmedQuery : null;

    currentPage = 1;
    hasMorePosts = true;
    isInitialLoad = true; 
    firstUnreadPostIdOnLoad = null;

    const postsContainer = document.getElementById('posts-container');
    if (postsContainer) {
        postsContainer.innerHTML = '<div class="loading">Поиск...</div>';
    }

    loadPosts();
}

async function scrollToPost(postId) {
    let postElement = document.getElementById(`post-${postId}`);
    const postsContainer = document.getElementById('posts-container');

    if (!postsContainer) return;

    while (!postElement && hasMorePosts && !isLoading) {
        await loadPosts();
        postElement = document.getElementById(`post-${postId}`);

        if (!postElement && hasMorePosts && !isLoading) {
            postsContainer.scrollTo({
                top: -(postsContainer.scrollHeight - postsContainer.scrollTop - postsContainer.clientHeight),
                behavior: 'smooth'
            });

            await new Promise(resolve => setTimeout(resolve, 300));
        }
    }

    if (postElement) {
        const postTop = postElement.offsetTop - postsContainer.offsetTop;

        postsContainer.scrollTo({
            top: postTop,
            behavior: 'smooth'
        });

        postElement.style.backgroundColor = '#f4d03f20';
        setTimeout(() => {
            if (postElement) postElement.style.backgroundColor = '';
        }, 2000);
    } else {
        console.warn(`Пост с ID ${postId} не найден`);
    }
}



function toggleDropdown() {
    const dropdownCharacterMenu = document.getElementById('character-dropdown');
    if (dropdownCharacterMenu) {
        dropdownCharacterMenu.classList.toggle('show');
    }
}

function selectCharacter(item) {
    const characterId = item.getAttribute('data-character-id');
    const characterName = item.querySelector('span')?.innerText;

    if (characterId && characterName) {
        const dropdownToggle = document.getElementById('dropdown-toggle');
        const characterIdInput = document.getElementById('selected-character-id');

        if (dropdownToggle) dropdownToggle.innerText = characterName;
        if (characterIdInput) characterIdInput.value = characterId;

        toggleDropdown();
    }
}

function scrollBottomPostsContainer() {
    const postsContainer = document.getElementById('posts-container');
    if (postsContainer) {
        postsContainer.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }
}

function toggleDropdownPostMenu(button) {
    const dropdownMenu = button.closest('.custom-dropdown-post')?.querySelector('.dropdown-menu-post');
    const allDropdownMenus = document.querySelectorAll('.dropdown-menu-post');
    
    allDropdownMenus.forEach(menu => {
        if (menu !== dropdownMenu) {
            menu.classList.remove('show');
        }
    });

    if (dropdownMenu) {
        dropdownMenu.classList.toggle('show');
    }
}


document.addEventListener('click', function (event) {
    const allDropdownMenus = document.querySelectorAll('.dropdown-menu-post');
    allDropdownMenus.forEach(menu => {
        const customDropdown = menu.closest('.custom-dropdown-post');
        if (!customDropdown?.contains(event.target)) {
            menu.classList.remove('show');
        }
        if (event.target.dataset.closeDropdown === 'true' || event.target.closest('[data-close-dropdown="true"]')) {
            menu.classList.remove('show');
        }
    });
});


function confirmDelete(event, postId) {
    event.preventDefault();
    event.stopPropagation();
    currentPostId = postId;
    
    const modal = document.getElementById('delete-post-modal');
    if (modal) {
        modal.style.display = 'block';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const cancelDelete = document.getElementById('cancel-delete');
    const confirmDeleteBtn = document.getElementById('confirm-delete');

    if (cancelDelete) {
        cancelDelete.addEventListener('click', function () {
            const modal = document.getElementById('delete-post-modal');
            if (modal) modal.style.display = 'none';
            currentPostId = null;
        });
    }

    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function () {
            if (currentPostId) {
                const form = document.getElementById(`delete-post-form-${currentPostId}`);
                if (form) {
                    fetch(`/game-room/destroy/${currentPostId}`, {
                        method: 'POST', 
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector(`#delete-post-form-${currentPostId} input[name="_token"]`)?.value
                        },
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Ошибка при удалении.');
                        }
                        return response.json();
                    })
                    .then(data => {

                    })
                    .catch(error => {
                        console.error('Ошибка при удалении поста:', error);
                    })
                    .finally(() => {
                        const modal = document.getElementById('delete-post-modal');
                        if (modal) modal.style.display = 'none';
                        currentPostId = null;
                    });
                }
            }
        });
    }
});