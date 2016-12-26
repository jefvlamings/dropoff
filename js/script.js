console.log("script.js loaded");

var zoneid = $("#zoneid").text();
console.log(zoneid);

// preload content
$('#message').bind('keyup mouseup',function() {
	console.log("change");
	var value = $(this).val();

	var extension = value.split('.').pop();

	if(extension === 'jpg' || extension === 'png' || extension === 'gif' || extension === 'jpeg'){
		var output = "<img src='" + value + "'/>";
		$('#preview').html(output);	
	}
	else{
		$('#preview').html('<p></p>');	
		$('#preview').find('p').text(value);	
	}
  	
});

/*
 * Change zone name
 */
$('#title').live('change', function() {

		var zonename = $(this).val();
		console.log(zonename);

		$.ajax({
			type: "POST",
			url: BASE_URL + "controller/update_zone_name",
			data: {
				zoneid: zoneid,
				zonename:zonename
			},
			cache: false
		}).done(function() {
			console.log("zone name updated");
			read();
		});

});

/*
 * Submit new snippet to server
 */
$('#message').live('keypress', function(e) {
    if(e.keyCode==13){       	 // 13 = enter

		var message = $("#message").val();

		$.ajax({
			type: "POST",
			url: BASE_URL + "controller/submit",
			data: {
				zoneid: zoneid,
				message:message
			},
			cache: false
		}).done(function() {
			console.log("Snippet submitted");
			read();
		});

		$("#message").val('');

	}
});

/*
 * Show comment form
 */
 $(".snippet").live("click",function(){
	
	this_input = $(this).find(".input");

	// remove comment form everywhere but here
	$(".input").not(this_input).addClass("hide");
	
	// show comment form only here
	$(this_input).removeClass("hide");	
	$(this_input).focus();


});

/*
 * Send comment server once "enter" is pressed
 */
$('.input').live('keypress', function(e) {
        if(e.keyCode==13){       	 // 13 = enter
			
			var comment = $(this).val();
        	var id = $(this).parent().parent().attr("id");
        	
        	send_comment(id,comment);
        	
        	$(this).val("");
        }
});

var send_comment = function(id,comment){

	console.log("comment: " + comment);
	console.log("id:" + id);

	$.ajax({
		type: "POST",
		url: BASE_URL + "controller/submit_comment",
		data: {
			id:id,
			comment:comment
		},
		cache: false
	}).done(function() {
		console.log("comment sent");
		read();
	});

};


/*
 * Delete snippet from server
 */
$(".snippet").live("dblclick",function(){

	var snippetid = $(this).attr("id");

	console.log("id is " + snippetid);

	$.ajax({
		type: "POST",
		url: BASE_URL + "controller/delete",
		data: {
			id:snippetid
		},
		cache: false
	}).done(function() {
		console.log("snippet deleted");
		read();
	});

});

/*
 * Build snippet presentation in columns
 */
var build_column = function(snippets){
	
	var output = "";

	output += "<div id='" + snippets['id'] + "' class='snippet'>" 
			+ 	image_or_text(snippets)
			+ 	"<div class='comments'>" 
			+ 		comment_output(snippets[0])
			+ 		"<input class='input hide' placeholder='respond'></input>"
			+ 	"</div>"
			+ "</div>";

	return output;
};

var image_or_text = function(snippets){
	if(snippets['type'] == 'image'){
		return "<img src='" + snippets['snippet'] + "'></img>"; 
	}
	else{
		return "<p>" + snippets['snippet'] + "</p>"; 
	}
}

var comment_output = function(comments){	
	
	var temp = "";	

	for(j=0;j<comments.length;j++){
		if(comments[j]['id'] !== 'x'){
			temp += "<div class='comment'><p>&quot" + comments[j]['comment'] + "&quot</p></div>";
		}
	}

	return temp;
}

/*
 * Display snippets
 */
var display = function(snippets){

	$(".snippet").not("#post").remove(); // clear columns

	for(i=0;i<snippets.length;i++){
		if(i%3 === 0){

			var output = build_column(snippets[i]);

			$("#one").append(output);
		}
		else if(i%3 === 1){

			var output = build_column(snippets[i]);

			$("#two").append(output);
		}
		else{

			var output = build_column(snippets[i]);

			$("#three").append(output);
		}
	}
	
};

/*
 * Fetch all snippets from server
 */
var read = function(){

	if(zoneid.length > 0){
		
		$.ajax({
			type: "GET",
			url: BASE_URL + "controller/read",
			data: {
				zoneid: zoneid
			},
			cache: false
		}).done(function(msg) {

			var temp = JSON.parse(msg); 
			display(temp);
			
		});
	}
};

/*
 * Display snippets on load
 */
read();




