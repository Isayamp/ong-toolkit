(function () {
    // Compare sans tenir compte des accents ni de la casse.
    function normaliser(texte) {
        return (texte || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
    }

    document.querySelectorAll('.amidac-rapports-bloc').forEach(function (bloc) {
        var recherche = bloc.querySelector('.amidac-filtres__recherche');
        var vide = bloc.querySelector('.amidac-filtres__vide');
        var sections = bloc.querySelectorAll('.amidac-docs');
        var choix = { categorie: '' };

        function filtrer() {
            var terme = normaliser(recherche.value.trim());
            var total = 0;

            sections.forEach(function (section) {
                var visibles = 0;

                section.querySelectorAll('[data-texte]').forEach(function (doc) {
                    var ok = !terme || normaliser(doc.dataset.texte).indexOf(terme) > -1;
                    doc.hidden = !ok;
                    if (ok) {
                        visibles++;
                    }
                });

                // Un groupe institutionnel sans document visible disparait aussi.
                section.querySelectorAll('.amidac-docs__groupe').forEach(function (groupe) {
                    groupe.hidden = !groupe.querySelector('li:not([hidden])');
                });

                var afficher = visibles > 0 && (!choix.categorie || section.dataset.categorie === choix.categorie);
                section.hidden = !afficher;
                if (afficher) {
                    total += visibles;
                }
            });

            vide.hidden = total > 0;
        }

        bloc.querySelectorAll('.amidac-filtres__groupe').forEach(function (groupe) {
            groupe.addEventListener('click', function (e) {
                var bouton = e.target.closest('button');
                if (!bouton) {
                    return;
                }
                choix[groupe.dataset.filtre] = bouton.dataset.valeur;
                groupe.querySelectorAll('button').forEach(function (b) {
                    b.setAttribute('aria-pressed', b === bouton ? 'true' : 'false');
                });
                filtrer();
            });
        });

        recherche.addEventListener('input', filtrer);
    });
})();
