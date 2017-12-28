(function () {
    const eZMfu = document.getElementById('ez-mfu');
    const bookmarkBtn = document.getElementById('content__sidebar_right__bookmark-tab');
    const bookmarkField = document.getElementById('bookmark-add_locationId');

    if (eZMfu && bookmarkBtn) {
        const dataLocationPath = eZMfu.dataset.parentLocationPath.split('/');
        const locationId = dataLocationPath[dataLocationPath.length - 2];
        bookmarkField.value = locationId;
    }
})();
