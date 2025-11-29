document.addEventListener('DOMContentLoaded', () => {
    console.log("¡Apple Store Perú: JavaScript épico (script.js) cargado! 🚀");

    const navbarHeight = 56; // Altura de la barra de navegación fija

    // Función auxiliar para mostrar la notificación Toast
    const showToast = (mensaje) => {
        const toastEl = document.getElementById('cartToast');
        const toastBody = toastEl ? toastEl.querySelector('.toast-body') : null;

        if (toastEl && toastBody) {
            toastBody.textContent = mensaje;
            const toast = new bootstrap.Toast(toastEl, {
                autohide: true,
                delay: 3500 
            });
            toast.show();
        }
    };

    // FUNCIÓN GLOBAL PARA EL CARRITO (Llamada desde index.php)
    window.addToCart = (productName, price, id) => {
        // En un proyecto real, aquí enviarías esta data a un archivo PHP/API usando fetch()
        
        console.log(`Producto añadido: ID=${id}, Nombre=${productName}, Precio=${price}`);
        
        // Feedback visual con Toast
        showToast(`✅ ¡${productName} añadido a tu bolsa!`);
    };
    
    // 1. FUNCIÓN DE DESPLAZAMIENTO SUAVE
    const scrollLinks = document.querySelectorAll('.nav-link[href^="#"], .scroll-link[href^="#"]');

    scrollLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();

            const targetId = link.getAttribute('href');
            const targetElement = document.querySelector(targetId);

            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - navbarHeight,
                    behavior: 'smooth'
                });

                // Cierra el menú de Bootstrap en el móvil
                const navbarCollapse = document.getElementById('navbarNav');
                if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                    const bsCollapse = new bootstrap.Collapse(navbarCollapse, { toggle: false });
                    bsCollapse.hide();
                }
            }
        });
    });

    // 2. MANUAL SCROLLSPY (Resalta el menú al hacer scroll)
    const sections = document.querySelectorAll('main section');
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');

    const setActiveLink = () => {
        let current = '';
        const scrollYOffset = window.scrollY; 

        sections.forEach(section => {
            // Usa el desplazamiento superior de la sección menos la altura de la barra de navegación
            const sectionTop = section.offsetTop - navbarHeight - 5;
            const sectionHeight = section.clientHeight;
            
            if (scrollYOffset >= sectionTop && scrollYOffset < sectionTop + sectionHeight) {
                current = `#${section.getAttribute('id')}`;
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            link.removeAttribute('aria-current');
            
            if (link.getAttribute('href') === current) {
                link.classList.add('active');
                link.setAttribute('aria-current', 'page');
            }
        });
    };

    // Llama al ScrollSpy en eventos de desplazamiento y cambio de tamaño
    window.addEventListener('scroll', setActiveLink);
    window.addEventListener('resize', setActiveLink);
    setActiveLink();
});