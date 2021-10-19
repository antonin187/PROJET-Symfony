var header = document.querySelector("header")
var Apic = document.querySelector('#Apic')
window.addEventListener('scroll', () => {
    if (window.scrollY > 500){
        header.style.backgroundColor = 'rgba(17, 17, 17, 1)'
        Apic.style.opacity = 1;
    }
})