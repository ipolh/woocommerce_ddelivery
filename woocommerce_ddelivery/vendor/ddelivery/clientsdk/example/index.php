<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<script src="http://sdk.ddelivery.ru/assets/js/ddelivery_v2.js"></script>
<form action="delivery.php" method="post" id="delivery_form">
    <div style="margin-top: 10px;">
        <a href="javascript:void(0)" id="select_way">Показать модуль</a>
    </div>
    <input type="hidden" name="sdk_id" id="sdk_id" value="" />
    <div  style="margin-top: 10px;">
        <button id="send_order">Кнопка отправки заказа</button>
    </div>
</form>


<div id="ddelivery_container_place"></div>
<script>
    var
        params = {
            url: 'ajax.php?action=module&city=151185',
            width: 550,
            height: 440
        },
        send_order = document.getElementById('send_order'),
        select_way = document.getElementById('select_way'),
        sdk_id_container = document.getElementById('sdk_id'),
        form = document.getElementById('delivery_form'),
        callbacks = {
            resize_event:function(data){
                // событие при изменению размеров модуля
                // в объекте data новые размеры
            },
            open: function(){
                // Хук на открытие окна;
                return true;
            },
            change: function(data){
                sdk_id_container.value = data.id;
                // Хук на окончание оформления заказа и обработка результата;
            },
            close_map: function(data){
                // Хук на закрытие карты
            },
            price: function(data){
                // хук на получение цены текущей доставки при переключении
                // и возможность НПП в этом пункте
            }
        };
    /**
     * Перед отправкой инициализируем модуль
     */
    select_way.onclick = function() {
        DDeliveryModule.init(params, callbacks, 'ddelivery_container_place');
    }
    /**
     * Перед отправкой скрипт проводит валидацию
     */

   send_order.onclick = function(){
        DDeliveryModule.sendForm({
            success:function(){
                alert("id заказа на сдк сервере " + sdk_id_container.value);
                if(parseInt(sdk_id_container.value) > 0 ){
                    form.submit();
                }else{
                    alert("Не заполнено поле");
                }
            },
            error:function(){
                alert(DDeliveryModule.getErrorMsg());
                return false;
            }
        });
        return false;
    };

</script>

</body>
</html>