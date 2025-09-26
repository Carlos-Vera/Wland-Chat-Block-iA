<?php


/**
 *
*/
function wland_enqueue_screen_style() 
{

      wp_enqueue_style('wland-chat-block-screen-css', plugin_dir_url( dirname(__FILE__) ) . '/assets/css/wland-chat-block-screen.css' );

      wp_enqueue_script( 'wland-chat-block-screen-js', plugin_dir_url( dirname(__FILE__) ) . '/assets/js/wland-chat-block-screen.js' );


}

add_action( "wp_enqueue_scripts", "wland_enqueue_screen_style" );

?>

        <div id="<?php echo $unique_id; ?>" class="braveslab-chat-widget-container position-<?php echo $position; ?>">
            <!-- Widget de Chat BravesLab -->
            <div id="braveslab-chat-container" class="chat-closed">
                <!-- Botón de toggle -->
                <button id="chat-toggle" title="Habla con nuestro asistente IA">
                    <div id="chat-lottie"></div>
                    <span id="close-icon" style="display: none;">✕</span>
                </button>
                
                <!-- Ventana de chat -->
                <div id="chat-window">
                    <!-- Header del chat -->
                    <div id="chat-header">
                        <div>
                            <h3><?php echo $header_title; ?></h3>
                            <p><?php echo $header_subtitle; ?></p>
                        </div>
                        <button id="close-chat">×</button>
                    </div>
                    
                    <!-- Mensajes -->
                    <div id="chat-messages">
                        <div class="message bot">
                            <div class="message-bubble">
                                <?php echo $welcome_message; ?>
                            </div>
                            <div class="message-time" id="welcome-time"></div>
                        </div>
                    </div>
                    
                    <!-- Indicador de escritura -->
                    <div class="typing-indicator" id="typing-indicator">
                        <div class="typing-dots">
                            <div class="typing-dot"></div>
                            <div class="typing-dot"></div>
                            <div class="typing-dot"></div>
                        </div>
                    </div>
                    
                    <!-- Input de mensaje -->
                    <div id="chat-input-container">
                        <input type="text" id="chat-input" placeholder="Escribe tu mensaje..." />
                        <button id="send-button" disabled>
                            <span>➤</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

       

       