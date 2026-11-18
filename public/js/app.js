(function () {
  const toggle = document.getElementById('mobileToggle');
  const nav = document.getElementById('mainNav');
  if (!toggle || !nav) return;

  toggle.addEventListener('click', function () {
    const isOpen = toggle.classList.toggle('is-open');
    nav.classList.toggle('is-open', isOpen);
    document.body.classList.toggle('menu-open', isOpen);
    toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    toggle.setAttribute('aria-label', isOpen ? 'Закрыть меню' : 'Открыть меню');
  });

  nav.querySelectorAll('a').forEach(function (link) {
    link.addEventListener('click', function () {
      if (!nav.classList.contains('is-open')) return;
      toggle.classList.remove('is-open');
      nav.classList.remove('is-open');
      document.body.classList.remove('menu-open');
      toggle.setAttribute('aria-expanded', 'false');
    });
  });
})();

// ====== СКРИПТ ДЛЯ LIGHTBOX (ПРОСМОТР ФОТО) ======
document.addEventListener('DOMContentLoaded', function() {
    // 1. Создаем HTML-структуру лайтбокса и добавляем её в конец body
    const lightboxHTML = `
        <div id="custom-lightbox" class="lightbox-overlay">
            <span class="lightbox-close">&times;</span>
            <img class="lightbox-img" src="" alt="Увеличенное изображение">
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', lightboxHTML);

    const lightbox = document.getElementById('custom-lightbox');
    const lightboxImg = lightbox.querySelector('.lightbox-img');
    const closeBtn = lightbox.querySelector('.lightbox-close');

    // 2. Находим все картинки в галерее, которые нужно открывать
    const galleryLinks = document.querySelectorAll('.collage-photo');

    // 3. Вешаем обработчик клика на каждую картинку
    galleryLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault(); // Отменяем переход по ссылке
            const imgSrc = this.getAttribute('href');
            lightboxImg.src = imgSrc;
            lightbox.classList.add('is-open');
            
            // Блокируем прокрутку сайта, пока открыто фото
            document.body.style.overflow = 'hidden'; 
        });
    });

    // 4. Функция закрытия
    function closeLightbox() {
        lightbox.classList.remove('is-open');
        document.body.style.overflow = ''; // Возвращаем прокрутку
        // Очищаем src с задержкой, чтобы не было "мигания" при закрытии
        setTimeout(() => {
            lightboxImg.src = ''; 
        }, 300); 
    }

    // Закрытие по клику на крестик
    closeBtn.addEventListener('click', closeLightbox);

    // Закрытие по клику на темный фон (вне картинки)
    lightbox.addEventListener('click', function(e) {
        if (e.target === lightbox) {
            closeLightbox();
        }
    });

    // Закрытие по нажатию на клавишу Esc (для удобства на ПК)
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && lightbox.classList.contains('is-open')) {
            closeLightbox();
        }
    });
});

// ====== СКРИПТ ДЛЯ ПЕРЕКЛЮЧЕНИЯ НЕДЕЛЬ В РАСПИСАНИИ ======
document.addEventListener('DOMContentLoaded', function() {
    const weekButtons = document.querySelectorAll('.week-button-new');
    const scheduleRows = document.querySelectorAll('.schedule-row-new');

    if (weekButtons.length > 0 && scheduleRows.length > 0) {
        weekButtons.forEach(button => {
            button.addEventListener('click', function() {
                const weekIndex = this.getAttribute('data-week'); // '0' или '1'

                // 1. Переключаем активный класс у кнопок
                weekButtons.forEach(btn => btn.classList.remove('is-active'));
                this.classList.add('is-active');

                // 2. Обновляем ссылки и даты в каждой строке
                scheduleRows.forEach(row => {
                    // Получаем ссылку в зависимости от выбранной недели
                    const newHref = weekIndex === '0' 
                        ? row.getAttribute('data-href-first') 
                        : row.getAttribute('data-href-second');

                    // Получаем дату в зависимости от выбранной недели
                    const dateElement = row.querySelector('.schedule-date');
                    const newDateText = weekIndex === '0' 
                        ? dateElement.getAttribute('data-date-first') 
                        : dateElement.getAttribute('data-date-second');

                    // Применяем новые значения
                    if (newHref) {
                        row.setAttribute('href', newHref);
                    }
                    if (newDateText) {
                        dateElement.textContent = newDateText;
                    }
                });
            });
        });
    }
});