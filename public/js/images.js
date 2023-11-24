const addFormToCollection = (e) => {
    console.log('test addFormToCollection');

    const collectionPhoto = document.querySelector(e.currentTarget.dataset.collection);

    const item = document.createElement('div');
    item.className = 'mt-3';

    const label = document.createElement("h4");
    label.innerHTML = "Photo " + (parseInt(collectionPhoto.dataset.index) + 1);
    collectionPhoto.appendChild(label);

    item.innerHTML = collectionPhoto
        .dataset
        .prototype
        .replace(
            /__name__/g,
            collectionPhoto.dataset.index
        );

        let btnDelete = document.createElement('button');
        btnDelete.className = 'btn btn-danger mt-3 mb-3 js-btn-delete';
        btnDelete.innerHTML = 'X';

        // ここでlabelをbtnDeleteに関連付け
        btnDelete.label = label;

        btnDelete.addEventListener('click', (e) => {
            e.currentTarget.parentElement.remove();
            // 削除時に関連付けられたlabelも削除
            e.currentTarget.label.remove();
        });

        item.appendChild(btnDelete);

        collectionPhoto.append(item);
        collectionPhoto.dataset.index++;

        document.querySelectorAll('.js-btn-delete').forEach(btn => btn.addEventListener('click', (e) =>
            e.currentTarget.parentElement.remove()
        ))
}


document.querySelectorAll('.js-btn-add').forEach(btn => btn.addEventListener('click', addFormToCollection));