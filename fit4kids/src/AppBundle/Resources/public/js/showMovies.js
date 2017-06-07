$(function(){
$("#videoDiv").css("display","none");
var videoLinks = $(".videoLink")

    videoLinks.click(function(e){
        var link = e.target;
        e.preventDefault();
        var id = link.dataset.id;
        $.ajax({
          method: "GET",
          url: '/movie/getMoviePath',
          data: {id:id}
        })
        .done(function(d){
            if($("#videoDiv").css("display")=="none"){
                $("#videoDiv").css("display","block");
            }
            $("#video").attr("src",d.path);
         })
        .fail(function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
         })
    });
});