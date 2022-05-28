<section class="catalog container">
    <div class="catalog__breadcrumbs">
        <div class="breadcrumbs subtitle">
            <div class="breadcrumbs__link">
                Главная
            </div>
            <div class="breadcrumbs__link">
                Велосипеды
            </div>
        </div>
    </div>

    <div class="catalog__content">
        <div class="catalog__filters filters">
            <div class="filters__controls">
                <svg class="filters__control-item" width="18" height="18"
                     data-filters-open>
                    <use href="#turn"></use>
                </svg>

                <svg class="filters__control-item" width="18" height="18"
                     data-filters-hide>
                    <use href="#un-turn"></use>
                </svg>
            </div>

            <div class="filters__items" id="filter-items">
                <?php foreach ($filters as $filter): ?>
                    <div class="accordion filters__item" data-accordion data-filter-el
                         data-filter-code="<?= $filter['code'] ?>" data-filter-type="<?= $filter['type'] ?>">
                        <div class="accordion__header" data-accordion-header>
                            <span class="accordion__title body-1"><?= $filter['title'] ?></span>

                            <svg class="accordion__icon" width="10" height="5">
                                <use href="#arrow"></use>
                            </svg>
                        </div>

                        <div class="accordion__inner filters__checkboxes"
                             data-accordion-inner>
                            <?php if ($filter['type'] === 'checkbox'): ?>
                                <?php foreach ($filter['items'] as $checkbox): ?>
                                    <label class="checkbox">
                                        <input class="checkbox__native"
                                               type="checkbox" name="<?=$checkbox['code']?>"
                                               data-filter-item>
                                        <!--                                           ${options.isPicked ? 'checked' : ''}>-->

                                        <span class="checkbox__box"></span>

                                        <span class="checkbox__text"><?=$checkbox['title']?></span>
                                    </label>
                                <?php endforeach;?>
                            <?php else: ?>
                                <div class="range-el" style='--left: <?=$filter['min']?>; --right: <?=$filter['max']?>; --min: <?=$filter['min']?>; --max: <?=$filter['max']?>' data-range>
                                    <div class="range-el__inputs" role='group' aria-labelledby='multi-lbl'>
                                        <input type='range' min=<?=$filter['min']?> value=${left} max=<?=$filter['max']?> data-range-left data-filter-item>

                                        <input type='range' min=<?=$filter['min']?> value=${right} max=<?=$filter['max']?> data-range-right>

                                        <div class="range-el__bg-line"></div>
                                    </div>
                                    <div class="range-el__outputs">
                                        <output style='--c: <?=$filter['min']?>'></output>
                                        <output style='--c: <?=$filter['max']?>'></output>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <button class="filters__btn body-2" data-filters-reset>
                сбросить все
            </button>
        </div>

        <div class="catalog__inner">
            <div class="catalog__header">
                <h1 class="h1">Велосипеды</h1>

                <div class="catalog__controls">
                    <button class="catalog__filter-btn button button-1">
                        <svg class="svg-primary" width="18" height="12">
                            <use href="#filter"></use>
                        </svg>

                        <span>Фильтр</span>
                    </button>

                    <div class="catalog__sort">
                        <span class="catalog__sort-title">Сортировать</span>

                        <div class="select body-1" id="sort">
                            <div class="select__header" data-select-header>
                                    <span class="select__title" data-header-text>
                                        По алфавиту
                                    </span>

                                <svg class="select__icon" width="10"
                                     height="5">
                                    <use href="#arrow"></use>
                                </svg>
                            </div>

                            <div class="select__inner">
                                <div class="select__item"
                                     data-select-item="alp">
                                    По алфавиту
                                </div>

                                <div class="select__item"
                                     data-select-item="price-down">
                                    По цене по убыванию
                                </div>

                                <div class="select__item"
                                     data-select-item="price-up">
                                    По цене по возрастанию
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="catalog__items-wrapper">
                <div class="catalog__items" id="catalog-items">
                    <?php foreach ($catalog as $item): ?>
                        <div class="product-card">
                            <div class="product-card__inner">
                                <div class="product-card__controls">
                                    <svg class="svg-grey" width="24" height="22">
                                        <use href="#hearth"></use>
                                    </svg>

                                    <div class="product-card__basket ${item.inBasket ? 'product-card__basket_active' : ''}"
                                         data-basket-toggle="${item.inBasket ? '1' : ''}"
                                         data-item-id="<?= $item['id'] ?>">
                                        <svg class="svg-primary" width="20" height="20">
                                            <use href="#basket"></use>
                                        </svg>
                                    </div>
                                </div>

                                <img class="product-card__image"
                                     src="/img/<?= $item['image'] ?>"
                                     alt="Изображение">

                                <div class="product-card__description">
                                    <?= $item['description'] ?>
                                </div>

                                <div class="product-card__price">
									<span>
										<?= $item['price'] ?>
									</span>

                                    <svg class="svg-primary" width="17" height="17">
                                        <use href="#rub"></use>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="catalog__loader">
                    loading...
                </div>
            </div>

            <div class="catalog__pagination">
                <div class="catalog__pagination-pages-wrapper" id="pagination">
                </div>


                <div class="catalog__sort">
                    <span class="catalog__sort-title">Лимит</span>

                    <div class="select body-1" id="limit">
                        <div class="select__header" data-select-header>
                                    <span class="select__title" data-header-text>
                                        12
                                    </span>

                            <svg class="select__icon" width="10"
                                 height="5">
                                <use href="#arrow"></use>
                            </svg>
                        </div>

                        <div class="select__inner">
                            <div class="select__item"
                                 data-select-item="alp">
                                6
                            </div>

                            <div class="select__item"
                                 data-select-item="price-down">
                                12
                            </div>

                            <div class="select__item"
                                 data-select-item="price-up">
                                24
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="catalog__footer">
                SEO text. Lorem ipsum dolor sit amet, consectetuer
                adipiscing elit, sed diam nonummy nibh euismod tincidunt ut
                laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad
                minim veniam, quis nostrud exerci tation ullamcorper
                suscipit lobortis nisl ut aliquip ex ea commodo consequat.
                Duis autem vel eum iriure dolor in hendrerit in vulputate
                velit esse molestie consequat, vel illum dolore eu feugiat
                nulla facilisis at vero eros et accumsan et iusto odio
                dignissim qui blandit praesent luptatum zzril delenit augue
                duis dolore te feugait nulla facilisi.
                Lorem ipsum dolor sit amet, cons ectetuer adipiscing elit,
                sed diam nonummy nibh euismod tincidunt ut laoreet dolore
                magna aliquam erat volutpat. Ut wisi enim ad minim veniam,
                quis nostrud exerci tation ullamcorper suscipit lobortis
                nisl ut aliquip ex ea commodo consequat.
            </footer>
        </div>
    </div>
</section>

<script type="module" src="/js/index.js"></script>
