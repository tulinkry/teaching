$.nette.init();

if (!String.prototype.format) {
  String.prototype.format = function() {
    var args = arguments;
    return this.replace(/{(\d+)}/g, function(match, number) { 
      return typeof args[number] != 'undefined'
        ? args[number]
        : match
      ;
    });
  };
}


$(function(){
	$(".noshow").hide() 
	var show = $.fn.show
	$.fn.show = function ()
	{
		$(this).removeClass ( "hidden" );
		return show.apply(this,arguments);
	};
	var hide = $.fn.hide
	$.fn.hide = function ()
	{
		$(this).addClass ( "hidden" );
		return hide.apply(this,arguments);
	};

});

$(function(){

	$(".nav-link").click(function(){
		// $(".nav-link").parent().removeClass('active')
		// $('.display .panel.panel-default').hide();
		// $(this).parent().addClass('active')
		// $('#' + $(this).data('id')).show();


		// return false;
	})

	var converter = new showdown.Converter()

	$(".markdown").each(function(){
		if($(this).data('markdown-processed')) return;
		$(this).html(converter.makeHtml($(this).text()))

		$(this).data('markdown-processed', true)
	})

	$(".code-control .btn-input").click(function() {
		var $control = $(this).closest('.code-control')
		var $textarea = $(this).data('textarea')
		if(!$textarea) {
			var $textarea = $('<textarea>').addClass('form-control')
										.attr('placeholder', 'Input')

			$('<div>')
			.addClass('collapse')
			.prependTo($control.find('.results'))
			.append(
				$("<div>")
				.addClass('code-input panel-body')
				.append('<h5>Stdin</h5>')
				.append($textarea)
			);
		}

		$textarea.closest('.collapse').collapse('toggle');
		$(this).data('textarea', $textarea);
		return false;
	});

	$(".code-control .btn-compile").click(function(e) {
		var findDependencies = function($el) {
			var deps = [];
			$el
				.find('.code.dependency textarea[data-name]')
				.each(function() {
					var name = $(this).data('name');
					var value = $(this)
						.data('compile-module')
						.getDoc()
						.getValue();
					deps.push({ file: name, code: value });
				})	
			return deps;		
		}
		var $control = $(this).closest('.code-control')
		var $results = $control.find('.messages')
		var $input = $(this).siblings('.btn-input').first().data('textarea') || $()
		var input_text = $input.val() || ''
		var code = $control
					.find('.code.main textarea')
					.data('compile-module')
					.getDoc()
					.getValue()
		var deps = findDependencies($control); 
		var options_raw = $control.data('compiler-options-raw') || ''
		console.log(code)
		$.ajax({
			type: 'POST',
			url: 'http://melpon.org/wandbox/api/compile.json',
			data: JSON.stringify({ 
				code: code,
				stdin: input_text,
				compiler: 'gcc-head',
				codes: deps,
				'compiler-option-raw': options_raw,
			}),
			beforeSend: function () {
				$results.text('Compiling ...')
				$results.closest('.collapse').collapse('show')
			},
			timeout: 10000
		})
		.error(function(x) {
			$results.text("Can not connect to remote server")
		})
		.done(function(payload) {
			$results.empty();
			if(payload.compiler_message) {
				$results.append($("<h6>").text('Compile error:'))
				var $text = $("<pre>").append($("<code>").addClass('nohighlight').text(payload.compiler_message))
				if(payload.compiler_message.match(/error:/)) {
					$text.addClass('text-danger')
				}
				$results.append($text)
			}
			if(payload.program_output && payload.program_output.length > 0) {
				$results.append($("<h6>").text('Output:'))
				var $text = $("<pre>").append($("<code>").addClass('nohighlight').text(payload.program_output))
				$results.append($text)
			}

			if(payload.status) {
				$results.append($("<h6>").text('Exit code:'))
				var $text = $("<pre>").append($("<code>").addClass('nohighlight').text(payload.status))
				if(payload.status == 0) {
					$text.addClass('text-success')
				} else {
					$text.addClass('text-danger')
				}
				$results.append($text)

			}
		})
		return false
	})

	$("textarea.c").each(function() {
		$(this).data('compile-module', CodeMirror.fromTextArea(this, {
		        lineNumbers: true,
		        matchBrackets: true,
		        mode: "text/x-csrc"
		    }))
	})

	$("textarea.cpp").each(function() {
		$(this).data('compile-module', CodeMirror.fromTextArea(this, {
		        lineNumbers: true,
		        matchBrackets: true,
		        mode: "text/x-c++src"
		    }));
	})
})