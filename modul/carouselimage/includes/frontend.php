<?php 
$idcarousel     = 'carousel'.$id.uniqid();
$images         = $settings->multiple_photos;
$showdesktop    = $settings->showdesktop;
$showmobile     = $settings->showmobile;
?>

<div class="vdc-carouselimage vdc-carouselimage-<?php echo $id; ?>">	
	
    <?php if($images):?>

        <div id="<?php echo $idcarousel; ?>" class="loops">
            <?php foreach($images as $n => $data):?> 
                <div class="carouselimage-item px-2">
                    <img class="w-100" src="<?php echo wp_get_attachment_image_src($data, 'full')[0]; ?>" alt="">
                </div>
            <?php endforeach; ?>       
        </div>

        <script>
            jQuery(function($){
                $(document).ready(function(){
                $('#<?php echo $idcarousel; ?>').slick({
                    dots: false,
                    arrows: true,
                    infinite: true,
                    speed: 300,
                    autoplay: true,
                    autoplaySpeed: 4000,
                    slidesToShow: <?php echo $showdesktop; ?>,
                    slidesToScroll: 1,
                    responsive: [
                        {
                            breakpoint: 1000,
                            settings: {
                                slidesToShow: <?php echo $showdesktop; ?>,
                            }
                        },
                        {
                            breakpoint: 600,
                            settings: {
                                slidesToShow: <?php echo $showmobile; ?>,
                            }
                        },
                    ],
                    nextArrow:'<div class="position-absolute end-0 top-50 z-1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16"> <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/> </svg></div>',
                    prevArrow:'<div class="position-absolute start-0 top-50 z-1"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16"> <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/> </svg></div>',
                    });
                });
            });
        </script>

    <?php endif; ?>
	
</div>
