<?php
/* * * begin the session ** */
session_start();

if (!isset($_SESSION['user_id'])) {
    $message = 'You must be logged in to access this page';
} else {
    try {
        require './mysqlData.php';


        /*         * * select the users name from the database ** */
        $dbh = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        /*         * * $message = a message saying we have connected ** */

        /*         * * set the error mode to excptions ** */
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        /*         * * prepare the insert ** */
        $stmt = $dbh->prepare("SELECT username FROM users 
        WHERE id = :id");

        /*         * * bind the parameters ** */
        $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);

        /*         * * execute the prepared statement ** */
        $stmt->execute();

        /*         * * check for a result ** */
        $phpro_username = $stmt->fetchColumn();
        $_SESSION['user_name'] = $phpro_username;

        /*         * * if we have no something is wrong ** */
    } catch (Exception $e) {
        
    }
}
?>

<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;" charset="utf-8">
        <script src="resources.js"></script>
        <script src="raphael-min.js"></script>
        <script src="jquery-1.11.3.min.js"></script>
        <script src="jquery-ui.min.js"></script>
        <script src="jquery.ui.datepicker-es.js"></script>


        <link rel="stylesheet" type="text/css" href="CSS/jquery.ui.datepicker-style.css">
        <link rel="stylesheet" type="text/css" href="CSS/cerulean.css">

        <title>Gestión de espacios - Reserva</title>


    </head>
    <body>
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="/">Reservas Ujaen</a>
                </div>

                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <li class="active"><a href="/">Reservar<span class="sr-only">(current)</span></a></li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li><a href="validate.php">Solicitudes</a></li>
                            <li><a href="acepted.php">Aceptadas</a></li>
                            <li><a href="record.php">Historial</a></li>
                        <?php endif; ?>
                    </ul>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <ul class="nav navbar-nav navbar-right">
                            <li><a href="#" onclick="logout()" >Cerrar sesión</a></li>
                        </ul>
                    <?php else: ?>

                        <ul class="nav navbar-nav navbar-right">
                            <li><a href="login.php">Iniciar sesión</a></li>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </nav>

        <div class="container-fluid">
            <div class="row">

                <div id="blank" class="col-lg-1 col-md-1">

                </div>

                <div id="selector" class="col-lg-3 col-md-3">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <b class="panel-title">Universidad de Jaén</b>
                                </div>
                                <div class="panel-body">
                                    <p>Campus Las Lagunillas, s/n, 23071 Jaén</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <b class="panel-title">Espacios</b>
                                </div>
                                <div class="panel-body">
                                    <div id="panelHolder">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7 col-md-7">
                    <div class="row">
                        <div class="col-lg-12">
                            <div id="MainPanel" class="panel panel-primary">
                                <div class="panel-heading">
                                    <b class="panel-title">Disponibilidad</b>
                                </div>
                                <div class="panel-body">
                                    <div id="divControls">
                                        <input type="button" value="Hoy" id="btToday" class="left-align">
                                        <input type="button" value="<<" id="btPrev2" class="left-align">
                                        <input type="button" value="<" id="btPrev" class="left-align">
                                        <div id="divDate" class="left-align">
                                            <input id="inCalendar" class="left-align" type="text" readonly="readonly">
                                            <div class="left-align" id="divDay">Miércoles</div>
                                        </div>
                                        <input type="button" value=">" id="btNext" class="left-align">
                                        <input type="button" value=">>" id="btNext2" class="left-align">
                                        <select id="selGroups" onchange="refreshData()">
                                            <option value="08:00">08:00</option>
                                            <option value="08:30">08:30</option>
                                            <option value="09:00">09:00</option>
                                            <option value="09:30">09:30</option>
                                            <option value="10:00">10:00</option>
                                            <option value="10:30">10:30</option>
                                            <option value="11:00">11:00</option>
                                            <option value="11:30">11:30</option>
                                            <option value="12:00">12:00</option>
                                            <option value="12:30">12:30</option>
                                            <option value="13:00">13:00</option>
                                            <option value="13:30">13:30</option>
                                            <option value="14:00">14:00</option>
                                            <option value="14:30">14:30</option>
                                            <option value="15:00">15:00</option>
                                            <option value="15:30">15:30</option>
                                            <option value="16:00">16:00</option>
                                            <option value="16:30">16:30</option>
                                            <option value="17:00">17:00</option>
                                            <option value="17:30">17:30</option>
                                            <option value="18:00">18:00</option>
                                            <option value="18:30">18:30</option>
                                            <option value="19:00">19:00</option>
                                            <option value="19:30">19:30</option>
                                            <option value="20:00">20:00</option>
                                            <option value="20:30">20:30</option>
                                            <option value="21:00">21:00</option>
                                            <option value="21:30">21:30</option>
                                            <option value="22:00">22:00</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="canvas center-block" id="canvas1">
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-md-3 col-xs-3"></div>
                        <div class="col-lg-4 col-md-4 col-xs-4">
                            <img align="center" src="uploads/legend.png"> 
                        </div>
                    </div>
                </div>

                <div class="col-lg-1 col-md-1"></div>

                <div id="inventory" class="col-lg-4 col-md-3">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-primary">
                                <div id="inventoryHeader" class="panel-heading">
                                    <b id="roomSelected" class="panel-title">Inventario</b>
                                </div>
                                <div id="inventoryBody" class="panel-body">
                                    <p id="info"></p>
                                </div>
                            </div>
                            <div class="panel panel-primary">
                                <div id="disponibilityHeader" class="panel-heading">
                                    <b class="panel-title">Reserva &nbsp;&uarr;&darr;</b>
                                </div>
                                <div id="disponibilityBody" class="panel-body">
                                    <div id="tableclickable"></div>

                                    <table id="table" class="table">
                                        <tbody>
                                            <tr>
                                                <td id="08:00"><a class="btn btn-link"><div style="height:100%;width:100%">08:00</div></a></td>
                                                <td id="08:30"><a class="btn btn-link"><div style="height:100%;width:100%">08:30</div></a></td> 
                                                <td id="09:00"><a class="btn btn-link"><div style="height:100%;width:100%">09:00</div></a></td>
                                                <td id="09:30"><a class="btn btn-link"><div style="height:100%;width:100%">09:30</div></a></td> 
                                            </tr>
                                            <tr>
                                                <td id="10:00"><a class="btn btn-link"><div style="height:100%;width:100%">10:00</div></a></td>
                                                <td id="10:30"><a class="btn btn-link"><div style="height:100%;width:100%">10:30</div></a></td> 
                                                <td id="11:00"><a class="btn btn-link"><div style="height:100%;width:100%">11:00</div></a></td>
                                                <td id="11:30"><a class="btn btn-link"><div style="height:100%;width:100%">11:30</div></a></td> 
                                            </tr>
                                            <tr>
                                                <td id="12:00"><a class="btn btn-link"><div style="height:100%;width:100%">12:00</div></a></td>
                                                <td id="12:30"><a class="btn btn-link"><div style="height:100%;width:100%">12:30</div></a></td> 
                                                <td id="13:00"><a class="btn btn-link"><div style="height:100%;width:100%">13:00</div></a></td>
                                                <td id="13:30"><a class="btn btn-link"><div style="height:100%;width:100%">13:30</div></a></td> 
                                            </tr>
                                            <tr>
                                                <td id="14:00"><a class="btn btn-link"><div style="height:100%;width:100%">14:00</div></a></td>
                                                <td id="14:30"><a class="btn btn-link"><div style="height:100%;width:100%">14:30</div></a></td> 
                                                <td id="15:00"><a class="btn btn-link"><div style="height:100%;width:100%">15:00</div></a></td>
                                                <td id="15:30"><a class="btn btn-link"><div style="height:100%;width:100%">15:30</div></a></td> 
                                            </tr>
                                            <tr>
                                                <td id="16:00"><a class="btn btn-link"><div style="height:100%;width:100%">16:00</div></a></td>
                                                <td id="16:30"><a class="btn btn-link"><div style="height:100%;width:100%">16:30</div></a></td> 
                                                <td id="17:00"><a class="btn btn-link"><div style="height:100%;width:100%">17:00</div></a></td>
                                                <td id="17:30"><a class="btn btn-link"><div style="height:100%;width:100%">17:30</div></a></td> 
                                            </tr>
                                            <tr>
                                                <td id="18:00"><a class="btn btn-link"><div style="height:100%;width:100%">18:00</div></a></td>
                                                <td id="18:30"><a class="btn btn-link"><div style="height:100%;width:100%">18:30</div></a></td> 
                                                <td id="19:00"><a class="btn btn-link"><div style="height:100%;width:100%">19:00</div></a></td>
                                                <td id="19:30"><a class="btn btn-link"><div style="height:100%;width:100%">19:30</div></a></td> 
                                            </tr>
                                            <tr>
                                                <td id="20:00"><a class="btn btn-link"><div style="height:100%;width:100%">20:00</div></a></td>
                                                <td id="20:30"><a class="btn btn-link"><div style="height:100%;width:100%">20:30</div></a></td> 
                                                <td id="21:00"><a class="btn btn-link"><div style="height:100%;width:100%">21:00</div></a></td>
                                                <td id="21:30"><a class="btn btn-link"><div style="height:100%;width:100%">21:30</div></a></td> 
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div id="desplegable">
                    <a href="#" title="Desplegar menu" id="sidebar-toggle" onclick="changeState();
                            return false;">
                        <span class="bar"></span>
                        <span class="bar"></span>
                        <span class="bar"></span>
                    </a>
                </div>
                </body>
                </html>

                <script>
                    function logout() {
                        $.get("logout.php");
                        parent.window.location.reload();
                        return false;
                    }

                    function changeState() {
                        $("#inventory").fadeOut(function () {
                            $("#desplegable").fadeOut(function () {
                                $("#blank").fadeOut(function () {
                                    $("#selector").fadeIn(150);
                                });
                            });
                        });
                    }

                    function rfc3986EncodeURIComponent(str) {
                        return encodeURIComponent(str).replace(/[!'()*]/g, escape);
                    }

                    function refreshData() {
                        if (lastObj)
                            refreshInfoTable();
                        var e = document.getElementById("selGroups");
                        var d = $("#inCalendar").datepicker('getDate');
                        var date = d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d.getDate() + " " + e.options[e.selectedIndex].value + ":00";
                        var save = currentImageName;
                        $.ajax({
                            url: 'mysqlphp.php',
                            type: 'POST',
                            data: {var1: date, var2: decodeURIComponent(currentImageName)},
                            success: function (data) {
                                var dataParsed = JSON.parse(data);
                                for (var j = 0; j < objects.length; j++) {
                                    objects[j].state.attr({'fill': '#00FF00'});
                                }
                                for (var i = 0; i < dataParsed.length; i++) {
                                    for (var j = 0; j < objects.length; j++) {
                                        if (save === currentImageName)
                                            if (dataParsed[i].node === String(objects[j].node)) {
                                                switch (dataParsed[i].state) {
                                                    case 'Reserved':
                                                        objects[j].state.attr({'fill': '#FF0000'});
                                                        break;
                                                    case 'Revising':
                                                        objects[j].state.attr({'fill': '#FFD700'});
                                                        break;
                                                    case 'Bloqued':
                                                        objects[j].state.attr({'fill': '#696969'});
                                                        break;
                                                }

                                            }
                                    }
                                }
                            }
                        });
                    }

                    var image;
                    var currentId = -1;
                    var currentImageName;
                    var objectsArray = [];
                    var objects = [];
                    var canvas = document.getElementById("canvas1");
                    var c = {
                        width: canvas.offsetWidth,
                        height: canvas.offsetHeight
                    };
                    var paper = Raphael(canvas, c.width, c.height);
                    var weekday = new Array(7);
                    weekday[0] = "Lunes";
                    weekday[1] = "Martes";
                    weekday[2] = "Miércoles";
                    weekday[3] = "Jueves";
                    weekday[4] = "Viernes";
                    weekday[5] = "Sábado";
                    weekday[6] = "Domingo";
                    var actualDate;
                    var minDate;
                    var maxDate;
                    $('#btPrev2').click(function () {
                        var temp;
                        temp = $("#inCalendar").datepicker('getDate');
                        temp.setDate(temp.getDate() - 7);
                        if (temp.valueOf() > minDate.valueOf()) {
                            loadFields(temp);
                        }
                    });
                    $('#btPrev').click(function () {
                        var temp;
                        temp = $("#inCalendar").datepicker('getDate');
                        temp.setDate(temp.getDate() - 1);
                        if (temp.valueOf() > minDate.valueOf()) {
                            loadFields(temp);
                        }
                    });
                    $('#btToday').click(function () {
                        $("#inCalendar").datepicker('setDate', new Date());
                        $('#divDay').html(weekday[($("#inCalendar").datepicker('getDate').getUTCDay()) ]);
                        refreshData();
                    });
                    $('#btNext').click(function () {
                        var temp;
                        temp = $("#inCalendar").datepicker('getDate');
                        temp.setDate(temp.getDate() + 1);
                        if (temp.valueOf() < maxDate.valueOf()) {
                            loadFields(temp);
                        }
                    });
                    $('#btNext2').click(function () {
                        var temp;
                        temp = $("#inCalendar").datepicker('getDate');
                        temp.setDate(temp.getDate() + 7);
                        if (temp.valueOf() < maxDate.valueOf()) {
                            loadFields(temp);
                        }
                    });
                    function loadFields(date) {
                        $("#inCalendar").datepicker('setDate', date);
                        $('#divDay').html(weekday[date.getUTCDay()]);
                        refreshData();
                    }

                    $(function () {
                        $("#inCalendar").datepicker({
                            changeMonth: true,
                            changeYear: true,
                            dateFormat: "dd-mm-yy",
                            minDate: 0,
                            maxDate: "+1Y",
                            onSelect: function (dateText, inst) {
                                $('#divDay').html(weekday[($(this).datepicker('getDate').getUTCDay())]);
                                refreshData();

                            }
                        });
                        $("#inCalendar").datepicker('setDate', new Date());
                        minDate = new Date();
                        maxDate = new Date();
                        minDate.setDate(minDate.getDate() - 1);
                        maxDate.setDate(maxDate.getDate() + 366);

                        $('#divDay').html(weekday[($("#inCalendar").datepicker('getDate').getUTCDay()) ]);

                    });


                    function addZero(i) {
                        if (i < 10) {
                            i = "0" + i;
                        }
                        return i;
                    }

                    function getMetaWidth(url) {
                        $("<img/>").attr("src", url).load(function () {
                            return(this.width);
                        });
                    }

                    function getMetaHeight(url) {
                        $("<img/>").attr("src", url).load(function () {
                            return(this.height);
                        });
                    }

                    function getMeta(url) {
                        alert("Yoh");
                        $("<img/>").attr("src", url).load(function () {
                            alert(this.width + ' ' + this.height);
                        });
                    }

                    function changeImg(id, name) {

                        if (currentId === id) {
                            refreshData();
                            return false;
                        } else {
                            currentImageName = name;
                            currentId = id;
                        }



                        var element = document.getElementById('img' + id);

                        element.onload = function () {
                            if (image) {
                                image.remove();
                            }
                            var width = getOriginalWidthOfImg(element);
                            var height = getOriginalHeightOfImg(element);
                            canvas.style.width = width;
                            canvas.style.height = height;
                            paper.setSize(width, height);
                            c = {
                                width: canvas.offsetWidth,
                                height: canvas.offsetHeight,
                                center: {x: canvas.offsetWidth / 2, y: canvas.offsetHeight / 2}
                            };

                            image = paper.image(element.src, 0, 0, width, height);

                            for (var i = 0; i < objectsArray.length; i++) {
                                if (objectsArray[i].id === id) {
                                    objects = objectsArray[i].objects;
                                }
                            }


                            refreshData();
                            toFront(objects);
                            toShow(objects);
                            hideExceptId(id);
                        };
                        element.src = element.src;
                    }

                    $.get("./Base.txt", function (v) {
                        $("#inventory").fadeOut(0);
                        $("#desplegable").fadeOut(0);
                        $("#blank").fadeOut(0);

                        var data = JSON.parse(v);
                        var lastPlant;


                        for (var i = 0; i < data.length; i++) {
                            for (var j = 0; j < data[i].length; j++)
                                if (data[i][j].id === "image") {
                                    $('#panelHolder').append('<a title="' + data[i][j].name + '" href="#" class="btnMainMenu right"\n\
                                        onclick="changeImg(' + i + ',\'' + data[i][j].name + '\');return false;">\n\
                                    <span class="left title">' + data[i][j].name + '</span>\n\
                                    <span class="right icon">&nbsp;<span class="arrow-right"></span></span>\n\
                                    </a>');

                                    $('#panelHolder').append('<img id="img' + i + '" src="' + data[i][j].url + '" visibility: hidden>');
                                    lastPlant = data[i][j].name;
                                    if (i === 0) {
                                        currentImageName = rfc3986EncodeURIComponent(data[i][j].name);
                                    }


                                } else if (data[i][j].id === "object") {
                                    var obj = {form: paper.path(data[i][j].formPath).attr(JSON.parse(data[i][j].formAttr)),
                                        text: paper.text(data[i][j].textX, data[i][j].textY, data[i][j].textPath).attr(JSON.parse(data[i][j].textAttr)),
                                        //state: paper.rect(data[i][j].stateX, data[i][j].stateY, 15, 15),
                                        state: paper.circle(data[i][j].stateX + 7.5, data[i][j].stateY + 7.5, 7.5),
                                        plant: lastPlant,
                                        node: data[i][j].node
                                    };
                                    glowable(obj);
                                    objects.push(obj);
                                }
                            toHide(objects);
                            objectsArray.push({
                                id: i,
                                objects: objects
                            });
                            objects = [];
                        }
                    }).done(function () {
                        changeImg(0, currentImageName);
                    });

                    var lastObj;

                    var glowable = function (obj) {
                        obj.form.click(function () {
                            clickOnSite(obj);

                        }
                        );
                        obj.text.click(function () {
                            clickOnSite(obj);
                        }
                        );
                        obj.form.hover(
                                function () {
                                    obj.form.attr({'opacity': 0.8});
                                    obj.g = obj.form.glow({
                                        color: "#F00",
                                        width: 10
                                    });
                                },
                                function () {
                                    obj.form.attr({'opacity': 1});
                                    obj.g.remove();
                                });
                        obj.text.hover(
                                function () {
                                    obj.form.attr({'opacity': 0.8});
                                    obj.g = obj.form.glow({
                                        color: "#F00",
                                        width: 10
                                    });
                                },
                                function () {
                                    obj.form.attr({'opacity': 1});
                                    obj.g.remove();
                                });
                    };

                    /*Funciones de controles de elementos*/
                    {
                        function getOriginalWidthOfImg(img_element) {
                            var t = new Image();
                            t.src = (img_element.getAttribute ? img_element.getAttribute("src") : false) || img_element.src;
                            return t.naturalWidth;
                        }

                        function getOriginalHeightOfImg(img_element) {
                            var t = new Image();
                            t.src = (img_element.getAttribute ? img_element.getAttribute("src") : false) || img_element.src;
                            return t.naturalHeight;
                        }

                        function toFront(array) {
                            for (var i = 0; i < array.length; i++) {
                                array[i].form.toFront();
                                array[i].text.toFront();
                                array[i].state.toFront();
                            }
                        }

                        function toShow(array) {
                            for (var i = 0; i < array.length; i++) {
                                array[i].form.show();
                                array[i].text.show();
                                array[i].state.show();
                            }
                        }

                        function hideExceptId(id) {
                            for (var i = 0; i < objectsArray.length; i++) {
                                if (objectsArray[i].id !== id) {
                                    toHide(objectsArray[i].objects);
                                }
                            }
                        }

                        function toHide(array) {
                            for (var i = 0; i < array.length; i++) {
                                array[i].form.hide();
                                array[i].text.hide();
                                array[i].state.hide();
                            }
                        }
                    }

                    function refreshInfoTable() {
                        var d = $("#inCalendar").datepicker('getDate');
                        var date = d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d.getDate() + " 00:00:00";
                        var dateShort = d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d.getDate();
                        $('#tableclickable').html("Reservar <b>" + arrNames[lastObj.node] + "</b> día: <b>" + dateShort + "</b></br>Click en una hora para empezar la reserva");
                        
        
                        $.ajax({
                            url: 'mysqlphpNode.php',
                            type: 'POST',
                            data: {var1: date, var2: decodeURIComponent(currentImageName), var3: lastObj.node},
                            success: function (data) {

                                var dataParsed = JSON.parse(data);

                                var nodeListA = document.getElementById("table");
                                var nodeList = nodeListA.getElementsByTagName("td");
                                for (var j = 0; j < nodeList.length; j++) {

                                    nodeList[j].style.background = "#00FF00";
                                    nodeList[j].style.cursor = "pointer";
                                    var n = nodeList[j].id.split(':');
                                    d.setHours(n[0]);
                                    d.setMinutes(n[1]);
                                    var date2 = d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d.getDate() + " " + addZero(d.getHours()) + ":" + addZero(d.getMinutes()) + ":00";


                                    nodeList[j].childNodes[0].href = "reserve.php?node=" + lastObj.node + "&site=" + currentImageName + "&date=" + date2;
                                    nodeList[j].childNodes[0].onclick = null;


                                    nodeList[j].childNodes[0].removeAttribute("disabled");

                                }

                                for (var i = 0; i < dataParsed.length; i++) {
                                    var start = new Date(dataParsed[i].startdate * 1000);
                                    var endD = new Date(dataParsed[i].enddate * 1000);
                                    while ((start.getHours() < endD.getHours()) || ((start.getHours() === endD.getHours()) && (start.getMinutes() < endD.getMinutes()))) {
                                        document.getElementById(addZero(start.getHours()) + ":" + addZero(start.getMinutes())).childNodes[0].href = "#";
                                        document.getElementById(addZero(start.getHours()) + ":" + addZero(start.getMinutes())).childNodes[0].setAttribute("disabled", "disabled");
                                        document.getElementById(addZero(start.getHours()) + ":" + addZero(start.getMinutes())).childNodes[0].onclick = function () {
                                            return false;
                                        };
                                        switch (dataParsed[i].state) {
                                            case 'Reserved':
                                                document.getElementById(addZero(start.getHours()) + ":" + addZero(start.getMinutes())).style.background = "#FF0000";
                                                document.getElementById(addZero(start.getHours()) + ":" + addZero(start.getMinutes())).childNodes[0].setAttribute("title", "Reservado");
                                                break;
                                            case 'Revising':
                                                document.getElementById(addZero(start.getHours()) + ":" + addZero(start.getMinutes())).style.background = "#FFD700";
                                                document.getElementById(addZero(start.getHours()) + ":" + addZero(start.getMinutes())).childNodes[0].setAttribute("title", "En revisión");
                                                break;
                                            case 'Bloqued':
                                                document.getElementById(addZero(start.getHours()) + ":" + addZero(start.getMinutes())).style.background = "#696969";
                                                document.getElementById(addZero(start.getHours()) + ":" + addZero(start.getMinutes())).childNodes[0].setAttribute("title", "Bloqueado");
                                                break;
                                        }
                                        start = new Date(start.getTime() + 30 * 60000);
                                    }
                                }

                            }
                        }

                        );

                    }

                    function clickOnSite(obj) {
                        lastObj = obj;

                        $("#selector").fadeOut(250, function () {
                            $("#inventory").fadeIn();
                            $("#desplegable").fadeIn();
                            $("#blank").fadeIn();
                        });
                        $('#roomSelected').html(arrNames[obj.node] + "&nbsp;&uarr;&darr;");

                        refreshInfoTable();

                        var list = document.getElementById("info");
                        var equip = arrEquip[obj.node];

                        if (equip[0] === "0") {
                            list.innerHTML = "<ul><li>Sin equipamiento</li></ul>";
                        } else {
                            var inner = "<ul>";




                            for (var i = 1; i < equip.length - 2; i++) {

                                if (equip[i] === "1") {
                                    inner += "<li>" + arrCheckFullName[i] + "</li>";
                                }
                            }

                            inner += "</ul>";

                            list.innerHTML = inner;
                        }



                    }
                    $(document).ready(function () {
                        $("#inventoryHeader").click(function (event) {
                            var desplegable = $(this).next();
                            $('inventoryHeader').not(desplegable).slideUp('fast');
                            desplegable.slideToggle('fast');
                            event.preventDefault();
                        });
                    });
                    $(document).ready(function () {
                        $("#disponibilityHeader").click(function (event) {
                            var desplegable = $(this).next();
                            $('disponibilityHeader').not(desplegable).slideUp('fast');
                            desplegable.slideToggle('fast');
                            event.preventDefault();
                        });
                    });
                </script>