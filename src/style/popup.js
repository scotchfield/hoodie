var pop = document.getElementById( 'popup' );

var xoffset = 20;
var yoffset = 0;

document.onmousemove = function(e) {
    var x, y, right, bottom;
  
    try { x = e.pageX; y = e.pageY; } // FF
    catch(e) { x = event.x; y = event.y + document.body.scrollTop; } // IE

    right = (document.documentElement.clientWidth || document.body.clientWidth || document.body.scrollWidth);
    bottom = (window.scrollY || document.documentElement.scrollTop || document.body.scrollTop) + (window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight || document.body.scrollHeight);

    x += xoffset;
    y += yoffset + 3;

    if (x > right-pop.offsetWidth - 3) {
        x = right - pop.offsetWidth - 3;
    }

    if (y > bottom-pop.offsetHeight - 3) {
        if (x < right - pop.offsetWidth - 3) {
            y = bottom - pop.offsetHeight - 3;
        } else {
            y = y - pop.offsetHeight - 10;
        }
    }

    pop.style.top = y+'px';
    pop.style.left = x+'px';
}

function popup(text) {
    pop.innerHTML = text;
    pop.style.display = 'block';
}

function popout() {
    pop.style.display = 'none';
}
