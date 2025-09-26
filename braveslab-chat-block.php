<?php
/**
 * Plugin Name: Wland Chat Block iA
 * Description: Pluging que permite integrar mediante un bloque de Gutenberg para integrar el chat de IA de BravesLab en cualquier página o entrada con integración a N8N.
 * Version: 1.0.1
 * Author: Carlos Vera (Carlos-Vera), Mikel Marqués (Ymikimonokia)
 * Text Domain: WebLan-chat
 * Plugin URI: https://github.com/Carlos-Vera/Wland-Chat-Block-iA.git
 * Contributors: Carlos Vera, Mikel Marqués
 * 
 * GitHub Plugin URI: Carlos-Vera/Wland-Chat-Block-iA
 * GitHub Branch: main
 * Requires WP: 5.0
 * Requires PHP: 7.4
 * Update supported: true
 */

// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

class BravesLabChatBlock {
    
    public function __construct() {
        add_action('init', array($this, 'register_block'));
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_block_assets'));
    }

    /**
     * Registrar el bloque de Gutenberg
     */
    public function register_block() {
        // Registrar el bloque
        register_block_type('braveslab/chat-widget', array(
            'editor_script' => 'braveslab-chat-block-js',
            'editor_style' => 'braveslab-chat-block-editor-css',
            'style' => 'braveslab-chat-block-css',
            'render_callback' => array($this, 'render_chat_block'),
            'attributes' => array(
                'webhookUrl' => array(
                    'type' => 'string',
                    'default' => 'https://flow.braveslab.com/webhook/1427244e-a23c-4184-a536-d02622f36325/chat'
                ),
                'welcomeMessage' => array(
                    'type' => 'string',
                    'default' => '¡Hola! Soy el asistente de BravesLab, tu Artificial Intelligence Marketing Agency. Integramos IA en empresas para multiplicar resultados. ¿Cómo podemos ayudarte?'
                ),
                'headerTitle' => array(
                    'type' => 'string',
                    'default' => 'BravesLab AI Assistant'
                ),
                'headerSubtitle' => array(
                    'type' => 'string',
                    'default' => 'Artificial Intelligence Marketing Agency'
                ),
                'position' => array(
                    'type' => 'string',
                    'default' => 'bottom-right'
                )
            )
        ));
    }

    /**
     * Encolar scripts y estilos para el editor
     */
    public function enqueue_block_assets() {
        wp_enqueue_script(
            'braveslab-chat-block-js',
            plugin_dir_url(__FILE__) . 'block.js',
            array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n'),
            '1.0.0'
        );

        wp_enqueue_style(
            'braveslab-chat-block-editor-css',
            plugin_dir_url(__FILE__) . 'block-editor.css',
            array(),
            '1.0.0'
        );

        wp_enqueue_style(
            'braveslab-chat-block-css',
            plugin_dir_url(__FILE__) . 'block-style.css',
            array(),
            '1.0.0'
        );
    }

    /**
     * Renderizar el bloque en el frontend
     */
    public function render_chat_block($attributes) {
        $webhook_url = esc_url($attributes['webhookUrl']);
        $welcome_message = esc_html($attributes['welcomeMessage']);
        $header_title = esc_html($attributes['headerTitle']);
        $header_subtitle = esc_html($attributes['headerSubtitle']);
        $position = esc_attr($attributes['position']);
        
        
        // Generar ID único para evitar conflictos
        $unique_id = 'braveslab-chat-' . uniqid();
        
        ob_start();
?>
<?php

            $full_screen = $attributes['fullScreen'] ? 'true' : 'false';  // NUEVA LÍNEA


            if ( $full_screen == true ) {
            require_once 'includes/screen.php';
            } 
            else {
            require_once 'includes/modal.php';
            }

?>
<?php
        return ob_get_clean();
    }
}

// Inicializar el plugin
new BravesLabChatBlock();

    // Crear archivos JavaScript y CSS necesarios para el bloque
add_action('init', function() {
    $plugin_dir = plugin_dir_path(__FILE__);
    
    // Crear block.js si no existe
    $block_js_file = $plugin_dir . 'block.js';
    if (!file_exists($block_js_file)) {
        $block_js_content = "(function(blocks, element, editor, components, i18n) {
    var el = element.createElement;
    var __ = i18n.__;
    var InspectorControls = editor.InspectorControls;
    var PanelBody = components.PanelBody;
    var TextControl = components.TextControl;
    var SelectControl = components.SelectControl;
    var TextareaControl = components.TextareaControl;

    blocks.registerBlockType('braveslab/chat-widget', {
        title: __('BravesLab AI Chat', 'braveslab-chat'),
        icon: 'format-chat',
        category: 'widgets',
        attributes: {
            webhookUrl: {
                type: 'string',
                default: 'https://flow.braveslab.com/webhook/1427244e-a23c-4184-a536-d02622f36325/chat'
            },
            welcomeMessage: {
                type: 'string',
                default: '¡Hola! Soy el asistente de BravesLab, tu Artificial Intelligence Marketing Agency. Integramos IA en empresas para multiplicar resultados. ¿Cómo podemos ayudarte?'
            },
            headerTitle: {
                type: 'string',
                default: 'BravesLab AI Assistant'
            },
            headerSubtitle: {
                type: 'string',
                default: 'Artificial Intelligence Marketing Agency'
            },
            position: {
                type: 'string',
                default: 'bottom-right'
            }
        },

        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            return el('div', {},
                el(InspectorControls, {},
                    el(PanelBody, {
                        title: __('Configuración del Chat', 'braveslab-chat'),
                        initialOpen: true
                    },
                        el(TextControl, {
                            label: __('URL del Webhook', 'braveslab-chat'),
                            value: attributes.webhookUrl,
                            onChange: function(value) {
                                setAttributes({webhookUrl: value});
                            }
                        }),
                        el(TextControl, {
                            label: __('Título del Header', 'braveslab-chat'),
                            value: attributes.headerTitle,
                            onChange: function(value) {
                                setAttributes({headerTitle: value});
                            }
                        }),
                        el(TextControl, {
                            label: __('Subtítulo del Header', 'braveslab-chat'),
                            value: attributes.headerSubtitle,
                            onChange: function(value) {
                                setAttributes({headerSubtitle: value});
                            }
                        }),
                        el(TextareaControl, {
                            label: __('Mensaje de Bienvenida', 'braveslab-chat'),
                            value: attributes.welcomeMessage,
                            onChange: function(value) {
                                setAttributes({welcomeMessage: value});
                            }
                        }),
                        el(SelectControl, {
                            label: __('Posición del Chat', 'braveslab-chat'),
                            value: attributes.position,
                            options: [
                                {label: 'Abajo Derecha', value: 'bottom-right'},
                                {label: 'Abajo Izquierda', value: 'bottom-left'},
                                {label: 'Centro', value: 'center'}
                            ],
                            onChange: function(value) {
                                setAttributes({position: value});
                            }
                        })
                    )
                ),
                el('div', {
                    className: 'braveslab-chat-block-preview',
                    style: {
                        border: '2px dashed #01B7AF',
                        borderRadius: '10px',
                        padding: '20px',
                        textAlign: 'center',
                        backgroundColor: '#CEF2EF',
                        color: '#242424'
                    }
                },
                    el('div', {
                        style: {
                            width: '60px',
                            height: '60px',
                            borderRadius: '50%',
                            background: 'linear-gradient(135deg, #01B7AF 0%, #5DD5C7 100%)',
                            margin: '0 auto 15px',
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                            fontSize: '24px'
                        }
                    }, '💬'),
                    el('h3', {
                        style: {
                            margin: '0 0 10px 0',
                            color: '#01B7AF'
                        }
                    }, attributes.headerTitle),
                    el('p', {
                        style: {
                            margin: '0 0 15px 0',
                            fontSize: '14px'
                        }
                    }, attributes.headerSubtitle),
                    el('div', {
                        style: {
                            backgroundColor: 'white',
                            borderRadius: '15px',
                            padding: '15px',
                            marginTop: '15px',
                            border: '1px solid rgba(1, 183, 175, 0.2)'
                        }
                    },
                        el('p', {
                            style: {
                                margin: 0,
                                fontSize: '13px',
                                fontStyle: 'italic'
                            }
                        }, 'Vista previa: ' + attributes.welcomeMessage.substring(0, 60) + '...')
                    ),
                    el('p', {
                        style: {
                            marginTop: '15px',
                            fontSize: '12px',
                            fontWeight: 'bold'
                        }
                    }, 'BravesLab AI Chat Widget - Posición: ' + (attributes.position === 'bottom-right' ? 'Abajo Derecha' : attributes.position === 'bottom-left' ? 'Abajo Izquierda' : 'Centro'))
                )
            );
        },

        save: function() {
            // El contenido se renderiza en PHP usando render_callback
            return null;
        }
    });
})(
    window.wp.blocks,
    window.wp.element,
    window.wp.blockEditor || window.wp.editor,
    window.wp.components,
    window.wp.i18n
);";
        
        file_put_contents($block_js_file, $block_js_content);
    }
    
    // Crear block-editor.css si no existe
    $block_editor_css_file = $plugin_dir . 'block-editor.css';
    if (!file_exists($block_editor_css_file)) {
        $block_editor_css_content = ".braveslab-chat-block-preview {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif ;
}

.braveslab-chat-block-preview h3 {
    font-weight: 600;
}

.wp-block-braveslab-chat-widget {
    margin: 20px 0;
}";
        
        file_put_contents($block_editor_css_file, $block_editor_css_content);
    }
    
    // Crear block-style.css si no existe
    $block_style_css_file = $plugin_dir . 'block-style.css';
    if (!file_exists($block_style_css_file)) {
        $block_style_css_content = "/* Estilos del bloque en el frontend */
.wp-block-braveslab-chat-widget {
    position: relative;
}

.braveslab-chat-widget-container {
    position: relative;
    width: 100%;
    min-height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
}";
        
        file_put_contents($block_style_css_file, $block_style_css_content);
    }
});