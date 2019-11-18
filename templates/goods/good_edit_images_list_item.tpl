<div  class="upload_good_image_bl" id="good_images_bl_{IMAGE_ID}">
        <a href="javascript:;" class="link" id="goods_images_upload_{NUM}">Загрузить</a> <span id="goods_images_uploaded_delete_{NUM}"> | <a href="javascript:;" class="link" onclick="delete_edit_good_images('{NUM}', '{IMAGE_ID}')">Удалить</a></span>
        <div id="goods_images_uploaded_image_{NUM}" image_id="{IMAGE_ID}" class="good_load_img_cont"><img id="good_image_{NUM}" src="{IMAGE_SRC}" /></div>
        <div id="goods_images_upload_proc_{NUM}"></div>
</div>

<script>
goods_image_upload_init('{NUM}')
</script>