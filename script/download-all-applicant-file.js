function downloadAllFiles() {
    const links = [
        'files/SeamanBook.pdf',
        'files/Competence.pdf',
        'files/SeamanVisa.pdf',
        'files/Certificate.pdf',
        'files/SeamanPassport.pdf',
        'files/Merits.pdf'
    ];

    links.forEach((file, i) => {
        const a = document.createElement('a');
        a.href = file;
        a.setAttribute('download', '');
        a.style.display = 'none';
        document.body.appendChild(a);
        setTimeout(() => {
            a.click();
            document.body.removeChild(a);
        }, i * 200); // stagger to prevent browser blocking
    });
}
