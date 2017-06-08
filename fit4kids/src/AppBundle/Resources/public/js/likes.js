$(function(){
var likeLinks = $(".likeLink")

    likeLinks.click(function(e){
        var link = this;
        e.preventDefault();
        var id = link.dataset.id;
        $.ajax({
          method: "GET",
          url: '/course/like',
          data: {id:id}
        })
        .done(function(d){
            var likeCounter = $(link).parent().find('span');
               
            likeCounter.html(d.likes);
         })
        .fail(function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
         })
    });
});