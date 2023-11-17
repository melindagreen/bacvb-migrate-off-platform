
/*** EXPORTS ****************************************************************/
 const save = (props) => {

    const {attributes} = props;
    const {mediaUrl, mediaAlt, logoUrl, logoAlt, title, info, buttonUrl, buttonText} = attributes;
    
    return (
        <div className="swiper-slide bc-wrapper__items" 
        data-title={title}
        data-info={info}
        data-buttontext={buttonText}
        data-buttonurl={buttonUrl}
        >
                 {logoUrl.length !== 0 && <img className="wp-block-mm-bradentongulfislands-wideslideshow__logo" data-load-type="img" 
                data-load-alt={logoAlt !== '' ? logoAlt : 'Carousel Image'}
                data-load-offset="lg"
                data-load-sm={logoUrl[2]} 
                data-load-md={logoUrl[1]}
                data-load-lg={logoUrl[0]}
                />}
            <img className="item-slide-img" data-load-type="img" 
                data-load-alt={mediaAlt !== '' ? mediaAlt : 'Carousel Image'}
                data-load-offset="lg"
                data-load-sm={mediaUrl[2]} 
                data-load-md={mediaUrl[1]}
                data-load-lg={mediaUrl[0]}
                />
        </div>
    );
 }

 export default save;