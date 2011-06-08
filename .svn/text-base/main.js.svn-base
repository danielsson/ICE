var $scroller = $('#scroller'), count = 0;
$(window).load(function() {
	if($scroller.length>0) {
		change();
	}
});

var msgs = [
"Company name inc. nominated for an oscar.",
"Company name inc. CIO quits, begin to work for GOOGly.",
'Our biggest competitor closed today. "We don\'t want anymore to get hurt", ex-president comments.'
]
function change() {
	$scroller.animate({opacity: 0}, 300, function() {
		if(count == msgs.length) { count = 0; }
		$scroller
			.text(msgs[count])
			.animate({opacity: 1}, 300);
		setTimeout("change();",5000);
		count++;
	});
}