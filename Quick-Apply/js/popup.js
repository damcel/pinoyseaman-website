function closePopup(popupId) {
    var popup = document.getElementById(popupId);
    if (popup) {
        popup.style.display = 'none';
    } else {
        console.error('Popup with ID ' + popupId + ' not found.');
    }
}

window.onload = function () {
    var errorPopup = document.getElementById('errorPopup');
    var successPopup = document.getElementById('successPopup');

    if (errorPopup) {
        errorPopup.style.display = 'flex';
    }

    if (successPopup) {
        successPopup.style.display = 'flex';
    }
};