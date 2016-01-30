## WordPress dynamic thumbnail for any images

### Usage

templates

    // define arguments
    <?php $args = "src=wp-content/uploads/image.jpg&width=64&height=64&background=FFFFFF";?>
    
    // retrieve string in to var
    <?php $uri = get_thumbnail($args);?>
    <img src="<?php echo $uri;?>" />
    
    // direct output to tag
    <img src="<?php the_thumbnail($args);?>" />
    
javascript

    <?php thumbnail_js();?>
    <script type=”javascript”>
    var thumbnail_src = get_thumbnail({
        src: "wp-content/uploads/image.jpg", 
        width: 64,
        height: 64,
        background: "FFFFFF"
    });
    </script>
    
available args

* width - thumbnail width in px
* height - thumbnail height in px
* background - background color, use **alpha** for transparent
* method - resize method (crop|fit|scale)
* type - type of output image (jpg|gif|png)
* quality - jpeg quality


