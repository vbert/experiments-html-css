// Ukrywanie top-bar przy scrollowaniu w dół i pokazanie przy scrollowaniu w górę
let lastScrollTop = 0;
const topBar = document.querySelector('.top-bar');

window.addEventListener('scroll', () => {
    let st = window.pageYOffset || document.documentElement.scrollTop;
    if (st > lastScrollTop && st > 50) {
    // scroll w dół -> ukryj top-bar
    topBar.classList.add('hidden');
    } else {
    // scroll w górę -> pokaż top-bar
    topBar.classList.remove('hidden');
    }
    lastScrollTop = st <= 0 ? 0 : st;
});

// Bottom bar mobile - rotacja info z napis "Napisz do nas"
const bottomBarInfo = document.querySelector('.bottom-bar .info-text');
if(bottomBarInfo) {
    const nowPlayingText = bottomBarInfo.querySelector('.now-playing');
    const writeUsBtn = document.querySelector('.write-us-btn');

    if(nowPlayingText && writeUsBtn){
    // Na mobile chcemy rotować co 5s:
    setInterval(() => {
        if(nowPlayingText.style.display === 'none') {
        nowPlayingText.style.display = 'inline';
        writeUsBtn.style.display = 'none';
        } else {
        nowPlayingText.style.display = 'none';
        writeUsBtn.style.display = 'inline-flex';
        }
    }, 5000);
    }
}

const header = document.querySelector('header');
// const topBar = document.querySelector('.top-bar');
const middleBar = document.querySelector('.middle-bar');
const bottomBar = document.querySelector('.bottom-bar');

// wysokość header
const headerHeight = header.getBoundingClientRect().height;
console.log('Header height:', headerHeight);

// wysokość poszczególnych barów
const topBarHeight = topBar ? topBar.getBoundingClientRect().height : 0;
const middleBarHeight = middleBar ? middleBar.getBoundingClientRect().height : 0;
const bottomBarHeight = bottomBar ? bottomBar.getBoundingClientRect().height : 0;

console.log('Top bar height:', topBarHeight);
console.log('Middle bar height:', middleBarHeight);
console.log('Bottom bar height:', bottomBarHeight);