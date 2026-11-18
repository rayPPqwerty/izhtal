document.addEventListener('DOMContentLoaded', function () {
    var toggle = document.getElementById('mobileToggle');
    var nav = document.getElementById('mainNav');

    if (toggle && nav) {
        toggle.addEventListener('click', function () {
            var opened = nav.classList.toggle('is-open');
            toggle.classList.toggle('is-open', opened);
            toggle.setAttribute('aria-expanded', opened ? 'true' : 'false');
            document.body.classList.toggle('menu-open', opened);
        });

        nav.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                nav.classList.remove('is-open');
                toggle.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
                document.body.classList.remove('menu-open');
            });
        });
    }

    var weekButtons = document.querySelectorAll('.week-button');
    weekButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            var secondWeek = button.dataset.week === '1';
            weekButtons.forEach(function (item) {
                item.classList.toggle('is-active', item === button);
            });
            document.querySelectorAll('.schedule-date').forEach(function (date) {
                date.textContent = secondWeek ? date.dataset.dateSecond : date.dataset.dateFirst;
            });
            document.querySelectorAll('.schedule-open').forEach(function (link) {
                link.href = secondWeek ? link.dataset.hrefSecond : link.dataset.hrefFirst;
            });
        });
    });
});
