(($) => {
    const T4CodeEditor = {
        init: init,
        get: get,
        showLoader: showLoader,
        ready: false,
        instances: {
            cssEditor: null,
            scssEditor: null,
            scssVariablesEditor: null,
            blockEditor: null,
            blockCssEditor: null,
        }
    };

    function init() {
        return new Promise((resolve, reject) => {
            if (T4CodeEditor.ready) {
                return resolve();
            }

            $('body').append(`
                <div class="t4-editor-loader" style="
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    z-index: 9999;
                    background-color: #000;
                    opacity: 0.3;
                "></div>
            `);

            const editorAssetsPath = 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs';

            require.config({
                paths: {
                    vs: editorAssetsPath
                }
            });

            require(['vs/editor/editor.main'], function () {
                T4CodeEditor.ready = true;

                $('.t4-editor-loader').remove();

                resolve();
            });
        });
    }

    async function get(options) {
        const { name, container, language } = options;

        await init();

        if (T4CodeEditor.instances[name]) {
            return T4CodeEditor.instances[name];
        }

        const editor = monaco.editor.create(document.querySelector(container), {
            value: '',
            language: language,
            minimap: { enabled: false },
        });

        T4CodeEditor.instances[name] = editor;

        return editor;
    }

    function showLoader() {
        $('.t4-editor-loader').css('display', 'block');
    }

    window.T4CodeEditor = T4CodeEditor;
})(jQuery)