function eventasticOverride( blockNamespace ){
  var config = jQuery('#calendar-container').data(); 
  var obj = {
    'sidebarBlock' : {},
    'calendarBlock' : {}    
  };
  obj.sidebarBlock.template = `
            <div class="events-card {{card_classes}}">
                <a href="{{event_url}}">
                <h1 style="color:black">SidebarBlock</h1>
                    <div class="wrapper">
                        <div class="content">`;                               
        if( config.cardconfig_showthumbnail ){
            obj.sidebarBlock.template += `<div class="image-wrapper" style="background-image:url({{event_image}});"></div>`;
        }                        
        obj.sidebarBlock.template += `
                            <div class="categories">{{event_categories}}</div>
                            <div class="date">{{event_date}} {{event_time}}</div>
                            <div class="upcoming-dates">{{event_upcoming_dates}}</div>
                            <div class="title">{{event_title}}</div>
                            <div class="location">{{event_address}}</div>
                        </div>
                    </div>
                </a>
            </div>
        `;
  obj.calendarBlock.template = `
            <div class="events-card {{card_classes}}">
                <a href="{{event_url}}">
                <h1 style="color:black">calBlock</h1>
                    <div class="wrapper">
                        <div class="content">`;                               
        if( config.cardconfig_showthumbnail ){
            obj.calendarBlock.template += `<div class="image-wrapper" style="background-image:url({{event_image}});"></div>`;
        }                        
        obj.calendarBlock
        .template += `
                            <div class="categories">{{event_categories}}</div>
                            <div class="date">{{event_date}} {{event_time}}</div>
                            <div class="upcoming-dates">{{event_upcoming_dates}}</div>
                            <div class="title">{{event_title}}</div>
                            <div class="location">{{event_address}}</div>
                        </div>
                    </div>
                </a>
            </div>
        `;        
  return obj[blockNamespace];
}
