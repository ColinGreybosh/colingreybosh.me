function burgerClick() {
    var burger = document.getElementById('burgerList');
    if (!isVisible('burgerList')) {
        burger.style.display = 'block';
    } else {
        burger.style.display = 'none';
    }
}

function isVisible(idName) {
    return document.getElementById(idName).style.display == 'block';
}
