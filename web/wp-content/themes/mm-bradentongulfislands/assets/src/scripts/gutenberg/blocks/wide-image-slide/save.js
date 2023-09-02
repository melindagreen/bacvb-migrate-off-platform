
/*** EXPORTS ****************************************************************/
 const save = (props) => {

    const {attributes} = props;
    const {mediaUrl, mediaAlt, title, info, buttonUrl} = attributes;
    
    return (
        <div className="swiper-slide bc-wrapper__items" 
        data-title={title}
        data-info={info}
        data-buttonurl={buttonUrl}
        >
            <img data-load-type="img" 
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