(function ($) {
    $(function () {
        var $categorie = $('#amidac_categorie');

        // N'affiche que les champs utiles à la catégorie choisie.
        function adapterChamps() {
            var choix = $categorie.val();
            $('[data-cats]').each(function () {
                var cats = ' ' + $(this).data('cats') + ' ';
                $(this).toggle(cats.indexOf(' ' + choix + ' ') > -1);
            });
        }
        $categorie.on('change', adapterChamps);
        adapterChamps();

        // Sélecteur de la médiathèque.
        $('#amidac-choisir-fichier').on('click', function (e) {
            e.preventDefault();
            var $bouton = $(this);
            var cadre = wp.media({
                title: $bouton.data('title'),
                button: { text: $bouton.data('button') },
                multiple: false
            });
            cadre.on('select', function () {
                var fichier = cadre.state().get('selection').first().toJSON();
                $('#amidac_fichier_url').val(fichier.url);
                $('#amidac_fichier_id').val(fichier.id);
            });
            cadre.open();
        });

        // Une adresse saisie à la main remplace le fichier de la médiathèque.
        $('#amidac_fichier_url').on('input', function () {
            $('#amidac_fichier_id').val('');
        });

        $(document).on('click', 'a[data-confirm]', function (e) {
            if (!window.confirm($(this).data('confirm'))) {
                e.preventDefault();
            }
        });

        $('#amidac-bulk').on('submit', function (e) {
            if ($(this).find('select[name="bulk"]').val() === 'delete' && !window.confirm($(this).data('confirm'))) {
                e.preventDefault();
            }
        });

        $('#amidac-select-all').on('change', function () {
            $('input[name="ids[]"]').prop('checked', this.checked);
        });
    });
})(jQuery);
