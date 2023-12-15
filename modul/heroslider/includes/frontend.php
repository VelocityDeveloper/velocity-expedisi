<?php
$idcarousel = 'carousel'.$id;
$sliders    = $settings->slider_columns;
?>

<div class="vdc-heroslider">
    <?php if($sliders): ?>
        <div id="<?php echo $idcarousel;?>" class="carousel slide carousel-fade" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php foreach( $sliders as $n => $slider): ?>
                    <div class="<?php echo $n<1?'carousel-item active':'carousel-item';?>">
                        
                        <div class="ratio ratio-21x9 bg-dark img-bg">
                            <?php if($slider->img): ?>
                                <?php echo wp_get_attachment_image( $slider->img, 'full', "", array( "class" => "img-fluid w-100" ) );  ?>
                            <?php endif; ?>
                        </div>
                        <div class="carousel-caption d-flex justify-content-center align-items-center top-0 bottom-0 z-2 text-white">
                            <div class="flex-fill">
                                <?php if($slider->title): ?>
                                    <h5 class="heroslide-title text-white fw-bold fs-2 mb-4">
                                        <?php echo $slider->title;?>
                                    </h5>
                                <?php endif; ?>
                                <?php if($slider->desc): ?>
                                    <div class="heroslide-caption">
                                        <?php
                                        $desc = $slider->desc;
                                        $desc = preg_replace('/<a(.*?)>/', '<a$1 class="btn btn-primary mt-2">', $desc);
                                        echo $desc;
                                        ?>
                                    </div>
                                <?php endif; ?>
                            </div>                          
                        </div>
                        <div class="overlayhero bg-dark position-absolute top-0 bottom-0 end-0 start-0 opacity-50 z-1"></div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="carousel-indicators">
                <?php foreach( $sliders as $n => $slider): ?>
                    <button type="button" data-bs-target="#<?php echo $idcarousel;?>" data-bs-slide-to="<?php echo $n;?>"<?php echo $n<1?'class="active" aria-current="true" ':' ';?> aria-label="Slide <?php echo $n+1;?>"></button>
                <?php endforeach; ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#<?php echo $idcarousel;?>" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#<?php echo $idcarousel;?>" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>

        <style>
            @media(max-width: 768px){
                #<?php echo $idcarousel;?> .img-bg {
                    --bs-aspect-ratio: 110%;
                }
            }

            .heroslide-title {
                opacity: 0;
                transform: translateY(-20px);
                animation: fadein 1s ease-in-out forwards;
            }
            .heroslide-caption {
                opacity: 0;
                transform: translateY(-20px);
                animation: fadein 1.5s ease-in-out forwards;
            }
            .heroslide-caption a.btn {
                opacity: 0;
                transform: translateY(-20px);
                animation: fadein 1.95s ease-in-out forwards;
            }


            @keyframes fadein {
                0% {
                    opacity: 0;
                    transform: translateY(-20px);
                }
                100% {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>

    <?php endif; ?>
</div>