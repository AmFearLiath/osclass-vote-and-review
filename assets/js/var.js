 $(document).ready(function(){
    var itemWidth = $("#vote_reviews").width();                
    var itemHeight = 0;

    $("#vote_reviews").children().each(function(){
        itemHeight = itemHeight + $(this).outerHeight(true) - 20;
    });

    if (itemHeight > 200) {
        $('#vote_reviews').bxSlider({
            mode: 'vertical',
            autoControls: true,
            ticker: true,
            useCSS: false,
            tickerHover: true,
            minSlides: 1,
            maxSlides: 3,
            speed: 8000,
            adaptiveHeight: false,
            slideMargin: 0,
            slideWidth: itemWidth
        });
    }
    
    var userWidth = $("#user_vote_reviews").width(),        
        userHeight = 0;

    $("#user_vote_reviews").children().each(function(){
        userHeight = userHeight + $(this).outerHeight(true);
    });

    if (userHeight > 200) {
        $('#user_vote_reviews').bxSlider({
            mode: 'vertical',
            autoControls: true,
            ticker: true,
            useCSS: false,
            tickerHover: true,
            minSlides: 1,
            maxSlides: 3,
            speed: 8000,
            adaptiveHeight: false,
            slideMargin: 0,
            slideWidth: userWidth
        });
    }
});