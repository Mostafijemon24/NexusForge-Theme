/**
 * FusionWrite AI workspace.
 */
( function () {
	'use strict';

	var D = window.fswrtData || {};
	var S = D.strings || {};

	// For browsers without :has(), so the full-page mode leaves no gap for the hidden toolbar.
	if ( document.body.classList.contains( 'fswrt-fullscreen' ) ) {
		document.documentElement.classList.add( 'fswrt-fullscreen-root' );
	}

	/* ------------------------------------------------------------ helpers */

	function t( key, a, b ) {
		var text = S[ key ] || key;
		return text.replace( '%1$s', a ).replace( '%2$s', b ).replace( '%s', a );
	}

	function $( selector, root ) {
		return ( root || document ).querySelector( selector );
	}

	function $$( selector, root ) {
		return Array.prototype.slice.call( ( root || document ).querySelectorAll( selector ) );
	}

	function formatNumber( n ) {
		return Number( n ).toLocaleString();
	}

	/**
	 * Call one of the plugin's AJAX actions. Resolves with the response data,
	 * rejects with an object that has a `message`.
	 */
	function post( action, data ) {
		var body = new FormData();
		body.append( 'action', 'fswrt_' + action );
		body.append( 'nonce', D.nonce );
		Object.keys( data || {} ).forEach( function ( key ) {
			body.append( key, data[ key ] );
		} );

		return fetch( D.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: body } ).then(
			function ( response ) {
				return response.text().then( function ( text ) {
					var json = null;
					try {
						json = JSON.parse( text );
					} catch ( e ) {
						json = null;
					}
					if ( ! json || ! json.success ) {
						throw ( json && json.data && json.data.message ) ? json.data : { message: t( response.ok ? 'badAnswer' : 'serverError' ) };
					}
					return json.data || {};
				} );
			},
			function () {
				throw { message: t( 'networkError' ) };
			}
		);
	}

	function toast( message, state ) {
		var holder = $( '#fw-toasts' );
		if ( ! holder || ! message ) {
			return;
		}
		var item = document.createElement( 'div' );
		item.className = 'fw-toast';
		item.dataset.state = state || 'ok';
		item.textContent = message;
		holder.appendChild( item );
		setTimeout( function () {
			item.remove();
		}, 'bad' === state ? 9000 : 4000 );
	}

	function setBusy( button, busy ) {
		if ( ! button ) {
			return;
		}
		button.classList.toggle( 'is-busy', busy );
		button.disabled = busy;
	}

	function showResult( line, message, state ) {
		if ( line ) {
			line.textContent = message || '';
			line.dataset.state = state || '';
		}
	}

	/** Wait a number of seconds, reporting the seconds left once a second. */
	function wait( seconds, onTick ) {
		return new Promise( function ( resolve ) {
			var left = Math.max( 0, Math.round( seconds ) );
			( function tick() {
				if ( left <= 0 ) {
					resolve();
					return;
				}
				if ( onTick ) {
					onTick( left );
				}
				left -= 1;
				setTimeout( tick, 1000 );
			}() );
		} );
	}

	var working = false;
	window.addEventListener( 'beforeunload', function ( event ) {
		if ( working ) {
			event.preventDefault();
			event.returnValue = '';
		}
	} );

	/* ------------------------------------------------------------ shared form parts */

	$$( '[data-fw-words-output]' ).forEach( function ( output ) {
		var range = $( '.fw-range', output.parentNode );
		function update() {
			output.textContent = t( 'aboutWords', formatNumber( range.value ) );
		}
		range.addEventListener( 'input', update );
		update();
	} );

	function readOptions() {
		var images = $( 'input[name="fw-images"]:checked' );
		return {
			provider: $( '#fw-provider' ).value,
			language: $( '#fw-language' ).value,
			tone: $( '#fw-tone' ).value,
			words: $( '#fw-words' ).value,
			custom: $( '#fw-custom' ).value.trim(),
			images: images ? parseInt( images.value, 10 ) : 0
		};
	}

	function imageLabel( slot, total ) {
		return 0 === slot ? t( 'stepFeatured' ) : t( 'stepImage', slot + 1, total );
	}

	function addImage( postId, slot, onWait, attempt ) {
		return post( 'add_image', { post_id: postId, slot: slot } ).catch( function ( error ) {
			if ( error.retry_after && ( attempt || 0 ) < 2 ) {
				return wait( error.retry_after + 1, onWait ).then( function () {
					return addImage( postId, slot, onWait, ( attempt || 0 ) + 1 );
				} );
			}
			throw error;
		} );
	}

	/**
	 * Write one post, then its images one by one.
	 *
	 * onStep( name, state, detail ) is called as things happen. `name` is
	 * "write" or "image0".."image2"; `state` is active, waiting, done or failed.
	 * Resolves with the post; rejects only when the text itself failed.
	 */
	function runPost( keyword, options, onStep ) {
		function write( attempt ) {
			return post( 'write_post', {
				keyword: keyword,
				provider: options.provider,
				language: options.language,
				tone: options.tone,
				words: options.words,
				custom: options.custom
			} ).catch( function ( error ) {
				if ( error.retry_after && ! attempt ) {
					return wait( Math.min( 90, error.retry_after + 1 ), function ( left ) {
						onStep( 'write', 'waiting', t( 'waitingProvider', left ) );
					} ).then( function () {
						onStep( 'write', 'active' );
						return write( 1 );
					} );
				}
				throw error;
			} );
		}

		onStep( 'write', 'active' );

		return write( 0 ).then( function ( result ) {
			onStep( 'write', 'done', t( 'wordsBy', formatNumber( result.words ), result.model ) );
			result.imageErrors = [];

			var chain = Promise.resolve();
			var slot;

			function queueImage( index ) {
				chain = chain.then( function () {
					var name = 'image' + index;
					function onWait( left ) {
						onStep( name, 'waiting', t( 'waitingImages', left ) );
					}
					var pause = index > 0 && D.images.wait ? wait( D.images.wait, onWait ) : Promise.resolve();
					return pause.then( function () {
						onStep( name, 'active' );
						return addImage( result.post_id, index, onWait );
					} ).then( function ( image ) {
						result.content = image.content;
						onStep( name, 'done' );
					}, function ( error ) {
						result.imageErrors.push( error.message );
						onStep( name, 'failed', error.message );
					} );
				} );
			}

			for ( slot = 0; slot < options.images; slot++ ) {
				queueImage( slot );
			}

			return chain.then( function () {
				return result;
			} );
		} );
	}

	/* ------------------------------------------------------------ write page */

	var writeForm = $( '#fw-write-form' );

	if ( writeForm ) {
		var topic = $( '#fw-topic' );
		var progress = $( '#fw-progress' );
		var progressList = $( '#fw-progress-list' );
		var result = $( '#fw-result' );
		var currentPost = 0;

		var fitTopic = function () {
			topic.style.height = 'auto';
			topic.style.height = topic.scrollHeight + 'px';
		};
		topic.addEventListener( 'input', fitTopic );
		topic.addEventListener( 'keydown', function ( event ) {
			if ( 'Enter' === event.key && ! event.shiftKey ) {
				event.preventDefault();
				writeForm.requestSubmit();
			}
		} );
		fitTopic();

		var editorFrame = function () {
			return window.tinymce ? window.tinymce.get( 'fswrt_editor' ) : null;
		};
		var setEditor = function ( html ) {
			var area = $( '#fswrt_editor' );
			var editor = editorFrame();
			if ( area ) {
				area.value = html;
			}
			if ( editor ) {
				editor.setContent( html );
			}
		};
		var getEditor = function () {
			var editor = editorFrame();
			if ( editor && ! editor.isHidden() ) {
				return editor.getContent();
			}
			return $( '#fswrt_editor' ).value;
		};

		writeForm.addEventListener( 'submit', function ( event ) {
			event.preventDefault();

			var keyword = topic.value.replace( /\s+/g, ' ' ).trim();
			var options = readOptions();

			if ( ! D.connected ) {
				toast( t( 'needProvider' ), 'bad' );
				return;
			}
			if ( ! keyword ) {
				toast( t( 'needTopic' ), 'bad' );
				topic.focus();
				return;
			}

			var steps = { write: t( 'stepWrite' ) };
			var i;
			for ( i = 0; i < options.images; i++ ) {
				steps[ 'image' + i ] = imageLabel( i, options.images );
			}

			progressList.textContent = '';
			Object.keys( steps ).forEach( function ( name ) {
				var item = document.createElement( 'li' );
				item.dataset.step = name;
				item.innerHTML = '<strong></strong><small></small>';
				item.firstChild.textContent = steps[ name ];
				progressList.appendChild( item );
			} );

			$( '#fw-progress-title' ).textContent = t( 'writingTopic', keyword );
			writeForm.hidden = true;
			progress.hidden = false;
			$( '#fw-progress-title' ).focus();
			working = true;

			runPost( keyword, options, function ( name, state, detail ) {
				var item = $( '[data-step="' + name + '"]', progressList );
				if ( item ) {
					item.dataset.state = 'waiting' === state ? 'active' : state;
					item.lastChild.textContent = detail || '';
				}
			} ).then( function ( data ) {
				working = false;
				currentPost = data.post_id;

				var note = t( 'saved_' + data.status ) + ' ' + t( 'wordsBy', formatNumber( data.words ), data.model );
				if ( data.imageErrors.length ) {
					note += ' ' + t( 'imagesMissing', data.imageErrors.length );
					toast( data.imageErrors[ 0 ], 'bad' );
				}

				$( '#fw-write' ).hidden = true;
				result.classList.remove( 'fw-offstage' );
				$( '#fw-result-note' ).textContent = note;
				$( '#fw-post-title' ).value = data.title;
				$( '#fw-open-editor' ).href = data.edit_url;
				$( '#fw-preview-post' ).href = data.view_url;
				setEditor( data.content );
				$( '.fw-head h1' ).textContent = t( 'reviewTitle' );
				$( '.fw-lede' ).textContent = t( 'reviewLede' );
				window.scrollTo( 0, 0 );
				$( '#fw-post-title' ).focus();
			}, function ( error ) {
				working = false;
				progress.hidden = true;
				writeForm.hidden = false;
				toast( error.message, 'bad' );
				topic.focus();
			} );
		} );

		$( '#fw-save-post' ).addEventListener( 'click', function () {
			var button = this;
			setBusy( button, true );
			post( 'update_post', {
				post_id: currentPost,
				title: $( '#fw-post-title' ).value,
				content: getEditor()
			} ).then( function ( data ) {
				toast( data.message );
			}, function ( error ) {
				toast( error.message, 'bad' );
			} ).then( function () {
				setBusy( button, false );
			} );
		} );
	}

	/* ------------------------------------------------------------ bulk page */

	var bulkForm = $( '#fw-bulk-form' );

	if ( bulkForm ) {
		var topicsField = $( '#fw-bulk-topics' );
		var bulkSubmit = $( '#fw-bulk-submit' );
		var stopRequested = false;

		var readTopics = function () {
			var seen = {};
			return topicsField.value.split( /\r?\n/ ).map( function ( line ) {
				return line.replace( /\s+/g, ' ' ).trim();
			} ).filter( function ( line ) {
				var key = line.toLowerCase();
				if ( ! line || seen[ key ] ) {
					return false;
				}
				seen[ key ] = true;
				return true;
			} ).slice( 0, 50 );
		};

		var updateCount = function () {
			var count = readTopics().length;
			$( '#fw-bulk-count' ).textContent = 1 === count ? t( 'oneTopic' ) : t( 'manyTopics', count );
			bulkSubmit.textContent = count > 1 ? t( 'writePosts', count ) : t( 'writeOnePost' );
		};
		topicsField.addEventListener( 'input', updateCount );
		updateCount();

		bulkForm.addEventListener( 'submit', function ( event ) {
			event.preventDefault();

			var topics = readTopics();
			var options = readOptions();

			if ( ! D.connected ) {
				toast( t( 'needProvider' ), 'bad' );
				return;
			}
			if ( ! topics.length ) {
				toast( t( 'needTopics' ), 'bad' );
				topicsField.focus();
				return;
			}

			var queue = $( '#fw-queue' );
			var title = $( '#fw-queue-title' );
			var written = 0;

			queue.textContent = '';
			topics.forEach( function ( keyword ) {
				var item = document.createElement( 'li' );
				item.innerHTML = '<strong></strong><small></small>';
				item.firstChild.textContent = keyword;
				item.lastChild.textContent = t( 'queued' );
				queue.appendChild( item );
			} );

			bulkForm.hidden = true;
			$( '#fw-queue-wrap' ).hidden = false;
			title.focus();
			working = true;
			stopRequested = false;

			var finish = function () {
				working = false;
				title.textContent = t( 'queueDone', written, topics.length );
				$( '#fw-queue-stop' ).hidden = true;
				$( '#fw-queue-again' ).hidden = false;
				$( '#fw-queue-hint' ).hidden = true;
				toast( t( 'queueDone', written, topics.length ) );
			};

			var next = function ( index ) {
				var item = queue.children[ index ];
				var j;

				if ( index >= topics.length || stopRequested ) {
					for ( j = index; j < topics.length; j++ ) {
						queue.children[ j ].dataset.state = 'skipped';
						queue.children[ j ].lastChild.textContent = t( 'notWritten' );
					}
					finish();
					return;
				}

				title.textContent = t( 'queueProgress', index + 1, topics.length );
				item.dataset.state = 'active';
				item.scrollIntoView( { block: 'nearest' } );

				runPost( topics[ index ], options, function ( name, state, detail ) {
					var label;
					if ( 'write' === name ) {
						label = t( 'stepWrite' );
					} else {
						label = imageLabel( parseInt( name.slice( 5 ), 10 ), options.images );
					}
					if ( 'active' === state || 'waiting' === state ) {
						item.lastChild.textContent = 'waiting' === state ? detail : label;
					}
				} ).then( function ( data ) {
					written++;
					item.dataset.state = 'done';
					item.firstChild.textContent = data.title;
					item.lastChild.textContent = t( 'saved_' + data.status ) + ( data.imageErrors.length ? ' ' + t( 'imagesMissing', data.imageErrors.length ) : '' ) + ' ';
					var link = document.createElement( 'a' );
					link.href = data.edit_url;
					link.target = '_blank';
					link.rel = 'noopener';
					link.textContent = t( 'edit' );
					item.lastChild.appendChild( link );
				}, function ( error ) {
					item.dataset.state = 'failed';
					item.lastChild.textContent = error.message;
				} ).then( function () {
					// A short breath between posts keeps free plans from refusing the next one.
					return index + 1 < topics.length && ! stopRequested ? wait( 4 ) : null;
				} ).then( function () {
					next( index + 1 );
				} );
			};

			next( 0 );
		} );

		$( '#fw-queue-stop' ).addEventListener( 'click', function () {
			stopRequested = true;
			this.disabled = true;
			this.textContent = t( 'stopping' );
		} );
	}

	/* ------------------------------------------------------------ model lists */

	function fillModels( field, models ) {
		var list = $( 'datalist', field );
		var freeOnly = $( '[data-fw-free-only]', field );
		var hint = $( '[data-fw-model-hint]', field );
		var free = models.filter( function ( model ) {
			return model.free;
		} );
		var shown = freeOnly && freeOnly.checked && free.length ? free : models;

		list.textContent = '';
		shown.forEach( function ( model ) {
			var option = document.createElement( 'option' );
			option.value = model.id;
			if ( model.label && model.label !== model.id ) {
				option.label = model.label;
			}
			list.appendChild( option );
		} );

		if ( hint ) {
			hint.textContent = free.length ? t( 'modelsLoadedFree', shown.length, free.length ) : t( 'modelsLoaded', shown.length );
		}
	}

	function loadModels( button, request ) {
		var field = button.closest( '.fw-field' );
		setBusy( button, true );
		return post( 'list_models', request ).then( function ( data ) {
			field.fwModels = data.models;
			fillModels( field, data.models );
			$( 'input[list]', field ).focus();
		}, function ( error ) {
			toast( error.message, 'bad' );
		} ).then( function () {
			setBusy( button, false );
		} );
	}

	$$( '[data-fw-free-only]' ).forEach( function ( box ) {
		box.addEventListener( 'change', function () {
			var field = box.closest( '.fw-field' );
			if ( field.fwModels ) {
				fillModels( field, field.fwModels );
			}
		} );
	} );

	/* ------------------------------------------------------------ providers page */

	$$( '.fw-provider' ).forEach( function ( box ) {
		var id = box.dataset.provider;
		var form = $( 'form', box );
		var line = $( '[data-fw-result]', box );
		var keyField = $( 'input[name="key"]', form );
		var baseField = $( 'input[name="base_url"]', form );

		$( '[data-fw-load-models]', box ).addEventListener( 'click', function () {
			loadModels( this, {
				provider: id,
				key: keyField.value,
				base_url: baseField ? baseField.value : ''
			} );
		} );

		$$( '[data-fw-preset]', box ).forEach( function ( preset ) {
			preset.addEventListener( 'click', function () {
				baseField.value = preset.dataset.fwPreset;
				baseField.focus();
			} );
		} );

		form.addEventListener( 'submit', function ( event ) {
			event.preventDefault();
			var button = $( 'button[type="submit"]', form );
			setBusy( button, true );
			showResult( line, t( 'testing' ) );

			post( 'save_provider', {
				provider: id,
				key: keyField.value,
				model: $( 'input[name="model"]', form ).value.trim(),
				base_url: baseField ? baseField.value.trim() : '',
				make_default: $( 'input[name="make_default"]', form ).checked ? '1' : '0'
			} ).then( function ( data ) {
				showResult( line, data.working ? data.message : t( 'savedButFailed', data.message ), data.working ? 'ok' : 'bad' );
				keyField.value = '';
				if ( data.masked ) {
					keyField.placeholder = data.masked;
				}
				$( '[data-fw-disconnect]', box ).hidden = false;
				$( '[data-fw-status]', box ).dataset.state = 'on';
				D.connected = Math.max( 1, D.connected );

				$$( '.fw-provider' ).forEach( function ( other ) {
					var status = $( '[data-fw-status]', other );
					var isDefault = other.dataset.provider === data[ 'default' ];
					$( 'input[name="make_default"]', other ).checked = isDefault;
					if ( 'on' === status.dataset.state ) {
						status.textContent = isDefault ? t( 'connectedDefault' ) : t( 'connected' );
					}
				} );
			}, function ( error ) {
				showResult( line, error.message, 'bad' );
			} ).then( function () {
				setBusy( button, false );
			} );
		} );

		$( '[data-fw-disconnect]', box ).addEventListener( 'click', function () {
			if ( ! window.confirm( t( 'confirmDisconnect' ) ) ) {
				return;
			}
			setBusy( this, true );
			post( 'disconnect_provider', { provider: id } ).then( function () {
				window.location.reload();
			}, function ( error ) {
				toast( error.message, 'bad' );
			} );
		} );
	} );

	if ( 0 === window.location.hash.indexOf( '#provider-' ) ) {
		var wanted = document.getElementById( window.location.hash.slice( 1 ) );
		if ( wanted ) {
			wanted.open = true;
		}
	}

	/* ------------------------------------------------------------ images page */

	var imagesForm = $( '#fw-images-form' );

	if ( imagesForm ) {
		var imagesLine = $( '[data-fw-result]', imagesForm );

		var showEngineFields = function () {
			var engine = $( 'input[name="engine"]:checked', imagesForm ).value;
			$$( '[data-fw-engine]', imagesForm ).forEach( function ( group ) {
				group.hidden = group.dataset.fwEngine !== engine;
			} );
			$$( '[data-fw-engine-not]', imagesForm ).forEach( function ( group ) {
				group.hidden = group.dataset.fwEngineNot === engine;
			} );
			$( '#fw-test-image' ).hidden = 'none' === engine;
		};
		$$( 'input[name="engine"]', imagesForm ).forEach( function ( radio ) {
			radio.addEventListener( 'change', showEngineFields );
		} );
		showEngineFields();

		$( '[data-fw-load-image-models]', imagesForm ).addEventListener( 'click', function () {
			loadModels( this, { provider: 'openrouter', want: 'image' } );
		} );

		var saveImages = function () {
			var data = {};
			new FormData( imagesForm ).forEach( function ( value, key ) {
				data[ key ] = value;
			} );
			return post( 'save_images', data );
		};

		imagesForm.addEventListener( 'submit', function ( event ) {
			event.preventDefault();
			var button = $( 'button[type="submit"]', imagesForm );
			setBusy( button, true );
			saveImages().then( function ( data ) {
				showResult( imagesLine, data.problem ? t( 'savedBut', data.problem ) : data.message, data.problem ? 'bad' : 'ok' );
			}, function ( error ) {
				showResult( imagesLine, error.message, 'bad' );
			} ).then( function () {
				setBusy( button, false );
			} );
		} );

		$( '#fw-test-image' ).addEventListener( 'click', function () {
			var button = this;
			var sample = $( '#fw-sample' );
			setBusy( button, true );
			showResult( imagesLine, t( 'makingSample' ) );

			saveImages().then( function () {
				return post( 'test_image' );
			} ).then( function ( data ) {
				$( 'img', sample ).src = data.src;
				sample.hidden = false;
				showResult( imagesLine, t( 'sampleReady' ), 'ok' );
			}, function ( error ) {
				showResult( imagesLine, error.retry_after ? t( 'sampleWait', error.message, error.retry_after ) : error.message, 'bad' );
			} ).then( function () {
				setBusy( button, false );
			} );
		} );
	}

	/* ------------------------------------------------------------ preferences page */

	var preferencesForm = $( '#fw-preferences-form' );

	if ( preferencesForm ) {
		var fullscreenBox = $( 'input[name="fullscreen"]', preferencesForm );
		var fullscreenBefore = fullscreenBox.checked;

		preferencesForm.addEventListener( 'submit', function ( event ) {
			event.preventDefault();
			var button = $( 'button[type="submit"]', preferencesForm );
			var data = {};
			new FormData( preferencesForm ).forEach( function ( value, key ) {
				data[ key ] = value;
			} );

			setBusy( button, true );
			post( 'save_preferences', data ).then( function ( saved ) {
				if ( fullscreenBox.checked !== fullscreenBefore ) {
					window.location.reload();
					return;
				}
				toast( saved.message );
			}, function ( error ) {
				toast( error.message, 'bad' );
			} ).then( function () {
				setBusy( button, false );
			} );
		} );
	}

	window.fswrtPost = post;
}() );
