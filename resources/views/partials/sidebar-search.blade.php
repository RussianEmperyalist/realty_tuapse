<div class="sidebar_inner">
    <div class="logo text-center">
        <a href="{{ route('home') }}">
            <div class="logo-img"><img src="{{ asset('legacy/themes/dolphin/assets/images/logo.png') }}" alt="Агентство недвижимости Туапсе"></div>
            <div class="logo-text slow">Агентство недвижимости "Туапсе"</div>
        </a>
    </div>
    <div class="search_index">
        <div class="h3 fint">Поиск</div>
        <form id="search-form" class="forma" action="{{ route('search') }}" method="get">
            <div class="search_content">
                <div class="index-header-form" id="search_form">
                    <div class="form-group">
                        <select class="width289 searchField less-opacity-control" id="city" name="city[]">
                            <option value="0">Выберите город</option>
                            <option value="9" @selected(in_array('9', (array) request()->input('city', []), true))>Туапсе</option>
                            <option value="10" @selected(in_array('10', (array) request()->input('city', []), true))>Туапсинский район</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <select class="form-control searchField" name="apType" id="apType">
                            <option value="0">Искать в разделе</option>
                            <option value="2" @selected(request('apType') == '2')>Продажа</option>
                            <option value="1" @selected(request('apType') == '1')>Аренда</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <select class="form-control searchField" name="objType" id="objType">
                            <option value="0">Тип недвижимости</option>
                            <option value="1" @selected(request('objType') == '1')>квартира</option>
                            <option value="3" @selected(request('objType') == '3')>комната</option>
                            <option value="2" @selected(request('objType') == '2')>дом</option>
                            <option value="4" @selected(request('objType') == '4')>земельный участок</option>
                            <option value="9" @selected(request('objType') == '9')>коммерция</option>
                            <option value="5" @selected(request('objType') == '5')>новостройка</option>
                            <option value="6" @selected(request('objType') == '6')>гостиница</option>
                            <option value="8" @selected(request('objType') == '8')>гараж</option>
                        </select>
                    </div>
                    <div class="pricebox row">
                        <div class="col-md-6 col-sm-6 col-xs-6">
                            <div class="form-group">
                                <input type="number" id="priceMin" name="price_min" class="form-control searchField" placeholder="Цена от" value="{{ request('price_min') }}">
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-6">
                            <div class="form-group">
                                <input type="number" id="priceMax" name="price_max" class="form-control searchField" placeholder="Цена до" value="{{ request('price_max') }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-6">
                            <div class="form-group">
                                <input type="number" id="squareMin" name="square_min" class="form-control searchField" placeholder="Площадь от" value="{{ request('square_min') }}">
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-6">
                            <div class="form-group">
                                <input type="number" id="squareMax" name="square_max" class="form-control searchField" placeholder="Площадь до" value="{{ request('square_max') }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <select class="form-control searchField" name="rooms" id="rooms">
                            <option value="0">Количество комнат</option>
                            <option value="1" @selected(request('rooms') == '1')>1</option>
                            <option value="2" @selected(request('rooms') == '2')>2</option>
                            <option value="3" @selected(request('rooms') == '3')>3</option>
                            <option value="4" @selected(request('rooms') == '4')>4 и более</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-xs-6">
                            <div class="form-group">
                                <input type="number" id="floorMin" name="floor_min" class="form-control searchField" placeholder="Этаж от" value="{{ request('floor_min') }}">
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-6">
                            <div class="form-group">
                                <input type="number" id="floorMax" name="floor_max" class="form-control searchField" placeholder="Этаж до" value="{{ request('floor_max') }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group mb-3 search-term">
                            <input class="textbox form-control searchField" id="search_term_text" maxlength="50" placeholder="Поиск по описанию или адресу" type="text" value="{{ request('term') }}" name="term">
                        </div>
                    </div>
                    <div class="form-group">
                        <input class="form-control searchField" placeholder="№ объявления" type="number" value="{{ request('sApId') }}" name="sApId" id="sApId">
                    </div>
                    <div class="form-group">
                        <div class="pretty p-default">
                            <input class="search-input-new searchField" id="search_with_photo" type="checkbox" value="1" name="wp" @checked(request()->boolean('wp'))>
                            <div class="state p-success">
                                <label for="search_with_photo">Только с фото</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="btn-search">
                    <div class="btn-search-bl">
                        <button type="submit" class="btn btn-primary btn-lg search_btn slow">
                            <i class="fas fa-search"></i> Найти
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<button type="button" class="close_btn_slide btn"><i class="far fa-times-circle"></i></button>
