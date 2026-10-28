(function () {
    // Compare sans tenir compte des accents ni de la casse.
    function normaliser(texte) {
        return (texte || '').normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();
    }

    document.querySelectorAll('.amidac-rapports-bloc').forEach(function (bloc) {
        var recherche = bloc.querySelector('.amidac-filtres__recherche');
        var categorie = bloc.querySelector('.amidac-filtres__categorie');
        var annee = bloc.querySelector('.amidac-filtres__annee');
        var vide = bloc.querySelector('.amidac-filtres__vide');
        var sections = bloc.querySelectorAll('.amidac-docs');

        function filtrer() {
            var terme = normaliser(recherche.value.trim());
            var cat = categorie ? categorie.value : '';
            var an = annee ? annee.value : '';
            var total = 0;

            sections.forEach(function (section) {
                var visibles = 0;

                section.querySelectorAll('[data-texte]').forEach(function (doc) {
                    var ok = (!terme || normaliser(doc.dataset.texte).indexOf(terme) > -1)
                        && (!an || doc.dataset.annee === an);
                    doc.hidden = !ok;
                    if (ok) {
                        visibles++;
                    }
                });

                // Un groupe institutionnel sans document visible disparaît aussi.
                section.querySelectorAll('.amidac-docs__groupe').forEach(function (groupe) {
                    groupe.hidden = !groupe.querySelector('li:not([hidden])');
                });

                var afficher = visibles > 0 && (!cat || section.dataset.categorie === cat);
                section.hidden = !afficher;
                if (afficher) {
                    total += visibles;
                }
            });

            vide.hidden = total > 0;
        }

        [recherche, categorie, annee].forEach(function (champ) {
            if (champ) {
                champ.addEventListener('input', filtrer);
                champ.addEventListener('change', filtrer);
            }
        });
    });
})();
