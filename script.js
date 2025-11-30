// Tēmas pārslēgšana
function toggleTheme() {
    document.body.classList.toggle('dark-mode');
    const btn = document.querySelector('.theme-toggle');
    const isDark = document.body.classList.contains('dark-mode');
    btn.textContent = isDark ? 'Gaišais' : 'Tumšais';
    
    // Saglabāt iestatījumu
    const theme = isDark ? 'dark' : 'light';
    try {
        localStorage.setItem('theme', theme);
    } catch (e) {
        console.log('Could not save theme preference');
    }
}

// Ielādēt tēmas iestatījumu lapas ielādes laikā
window.addEventListener('load', () => {
    try {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            document.body.classList.add('dark-mode');
            const btn = document.querySelector('.theme-toggle');
            if (btn) {
                btn.textContent = 'Gaišais';
            }
        }
    } catch (e) {
        console.log('Could not load theme preference');
    }
});

// Mobīlā izvēlne
function toggleMobile() {
    const nav = document.getElementById('mobileNav');
    nav.classList.toggle('active');
}

function closeMobile() {
    const nav = document.getElementById('mobileNav');
    nav.classList.remove('active');
}

// Mobīlā dropdown izvēlne
function toggleMobileDropdown() {
    const dropdown = document.getElementById('mobileDropdown');
    dropdown.classList.toggle('active');
}

// Meklēšana/Filtrēšana rakstiem
function filterPosts() {
    const query = document.getElementById('searchBox').value.toLowerCase();
    const cards = document.querySelectorAll('.post-card');
    
    cards.forEach(card => {
        const title = card.getAttribute('data-title').toLowerCase();
        const desc = card.getAttribute('data-desc').toLowerCase();
        
        if (title.includes(query) || desc.includes(query)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Modālo logu funkcijas
function showModal(title, text) {
    const modal = document.getElementById('modal');
    const modalTitle = document.getElementById('modalTitle');
    const modalText = document.getElementById('modalText');
    
    modalTitle.textContent = title;
    modalText.textContent = text;
    modal.classList.add('active');
    
    // Aizliegt ķermeņa ritināšanu
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    const modal = document.getElementById('modal');
    modal.classList.remove('active');
    
    // Atjaunot ķermeņa ritināšanu
    document.body.style.overflow = 'auto';
}

// Aizvērt modālo logu ar ESC taustiņu
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeModal();
    }
});

// Veidlapas apstiprināšana un iesniegšana
function handleSubmit(e) {
    e.preventDefault();
    
    const nameField = document.getElementById('userName');
    const emailField = document.getElementById('userEmail');
    const msgField = document.getElementById('userMsg');
    
    // Noņemt iepriekšējos kļūdu stāvokļus
    nameField.classList.remove('invalid');
    emailField.classList.remove('invalid');
    msgField.classList.remove('invalid');
    
    let isValid = true;
    
    // Validēt vārdu
    if (nameField.value.trim() === '') {
        nameField.classList.add('invalid');
        isValid = false;
    }
    
    // Validēt e-pastu
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(emailField.value.trim())) {
        emailField.classList.add('invalid');
        isValid = false;
    }
    
    // Validēt ziņojumu
    if (msgField.value.trim().length < 10) {
        msgField.classList.add('invalid');
        isValid = false;
    }
    
    // Ja validācija ir veiksmīga, parādīt veiksmīgas iesniegšanas ziņojumu
    if (isValid) {
        const successAlert = document.getElementById('successAlert');
        successAlert.classList.add('show');
        
        // Atiestatīt formu pēc 3 sekundēm
        setTimeout(() => {
            e.target.reset();
            successAlert.classList.remove('show');
        }, 3000);
    }
    
    return false;
}

// Newsletter abonēšana
function subscribeNewsletter() {
    const emailInput = document.querySelector('.newsletter input');
    const email = emailInput.value.trim();
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (emailPattern.test(email)) {
        alert('Paldies par pierakstīšanos!');
        emailInput.value = '';
    } else {
        alert('Lūdzu, ievadi derīgu e-pasta adresi');
    }
}

// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        
        // Neaizliegt modālo saišu noklusējuma iestatījumus
        if (href === '#' && this.classList.contains('read-more')) {
            return;
        }
        
        const target = document.querySelector(href);
        if (target) {
            e.preventDefault();
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});