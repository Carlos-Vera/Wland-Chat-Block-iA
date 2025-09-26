<!-- Incluir la librería Lottie -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js"></script>

 <script>
        (function() {
            // Evitar múltiples inicializaciones
            if (window.BravesLabChatInitialized) return;
            window.BravesLabChatInitialized = true;
            
            class BravesLabChat {
                constructor() {
                    this.webhookUrl = '<?php echo $webhook_url; ?>';
                    this.sessionId = this.generateSessionId();
                    this.initializeElements();
                }
                
                initializeElements() {
                    const maxAttempts = 10;
                    let attempts = 0;
                    
                    const tryInitialize = () => {
                        attempts++;
                        console.log(`BravesLab Chat - Intento de inicialización #${attempts}`);
                        
                        this.chatContainer = document.getElementById('braveslab-chat-container');
                        this.chatToggle = document.getElementById('chat-toggle');
                        this.chatWindow = document.getElementById('chat-window');
                        this.closeChat = document.getElementById('close-chat');
                        this.chatMessages = document.getElementById('chat-messages');
                        this.chatInput = document.getElementById('chat-input');
                        this.sendButton = document.getElementById('send-button');
                        this.typingIndicator = document.getElementById('typing-indicator');
                        
                        if (this.chatContainer && this.chatToggle && this.chatWindow && 
                            this.closeChat && this.chatMessages && this.chatInput && 
                            this.sendButton && this.typingIndicator) {
                            
                            console.log('BravesLab Chat - Todos los elementos encontrados, inicializando...');
                            this.isOpen = false;
                            this.isTyping = false;
                            this.init();
                            return;
                        }
                        
                        if (attempts < maxAttempts) {
                            setTimeout(tryInitialize, 500);
                        } else {
                            console.error('BravesLab Chat - No se pudieron encontrar todos los elementos después de', maxAttempts, 'intentos');
                        }
                    };
                    
                    if (document.readyState === 'loading') {
                        document.addEventListener('DOMContentLoaded', tryInitialize);
                    } else {
                        tryInitialize();
                    }
                }
                
                generateSessionId() {
                    return 'web_' + Math.random().toString(36).substr(2, 9) + '_' + Date.now();
                }
                
                init() {
                    document.getElementById('welcome-time').textContent = this.formatTime(new Date());
                    
                    this.chatToggle.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        this.toggleChat();
                    });
                    
                    this.chatToggle.addEventListener('touchstart', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        this.toggleChat();
                    });
                    
                    this.closeChat.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        this.closeWindow();
                    });
                    
                    this.chatInput.addEventListener('keypress', (e) => {
                        if (e.key === 'Enter' && !e.shiftKey) {
                            e.preventDefault();
                            this.sendMessage();
                        }
                    });
                    
                    this.chatInput.addEventListener('input', () => this.toggleSendButton());
                    
                    this.sendButton.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        this.sendMessage();
                    });
                    
                    this.chatInput.addEventListener('focus', () => {
                        this.scrollToBottom();
                    });
                    
                    console.log('BravesLab Chat initialized successfully');
                    this.initLottie();
                }
                initLottie() {
                    const animationPath = '<?php echo plugin_dir_url(__FILE__); ?>chat.json';
                    
                    this.lottieAnimation = lottie.loadAnimation({
                        container: document.getElementById('chat-lottie'),
                        renderer: 'svg',
                        loop: false,    // Cambiar a false para controlar manualmente
                        autoplay: false, // Cambiar a false para no reproducir automáticamente
                        path: animationPath
                    });
                    
                    this.lottieAnimation.addEventListener('DOMLoaded', () => {
                        // Cambiar colores si es necesario
                        const svgElements = document.querySelectorAll('#chat-lottie svg path[stroke="#545454"]');
                        svgElements.forEach(el => {
                            el.setAttribute('stroke', '#01B7AF');
                        });
                        
                        // Reproducir una vez al cargar
                        this.lottieAnimation.play();
                    });
                    
                    // Agregar eventos de hover
                    this.setupHoverAnimation();
                }

                setupHoverAnimation() {
                    // Agregar eventos de hover al botón de chat
                    this.chatToggle.addEventListener('mouseenter', () => {
                        if (this.lottieAnimation && !this.isOpen) {
                            // Solo animar si el chat está cerrado
                            this.lottieAnimation.goToAndPlay(0); // Reiniciar y reproducir desde el inicio
                        }
                    });
                    
                    this.chatToggle.addEventListener('mouseleave', () => {
                        if (this.lottieAnimation && !this.isOpen) {
                            // Detener en el último frame cuando no esté en hover
                            this.lottieAnimation.goToAndStop(this.lottieAnimation.totalFrames - 1);
                        }
                    });
                }

                toggleChat() {
                    console.log('Toggle chat clicked, current state:', this.isOpen);
                    this.isOpen = !this.isOpen;
                    
                    if (this.isOpen) {
                        this.openWindow();
                    } else {
                        this.closeWindow();
                    }
                }
                
                openWindow() {
                    console.log('Opening chat window');
                    this.chatWindow.style.display = 'flex';
                    this.chatContainer.classList.remove('chat-closed');
                    this.chatContainer.classList.add('chat-open');
                    this.chatInput.focus();
                    this.scrollToBottom();
                    this.isOpen = true;
                    document.getElementById('close-icon').style.display = 'block';
                    
                    // Pausar la animación cuando el chat esté abierto
                    if (this.lottieAnimation) {
                        this.lottieAnimation.pause();
                    }
                }

                closeWindow() {
                    console.log('Closing chat window');
                    this.chatWindow.style.display = 'none';
                    this.chatContainer.classList.remove('chat-open');
                    this.chatContainer.classList.add('chat-closed');
                    this.isOpen = false;
                    document.getElementById('close-icon').style.display = 'none';
                    
                    // Volver a habilitar la animación al cerrar el chat
                    if (this.lottieAnimation) {
                        this.lottieAnimation.goToAndStop(0); // Volver al primer frame
                    }
                }
                
                toggleSendButton() {
                    const hasText = this.chatInput.value.trim().length > 0;
                    this.sendButton.disabled = !hasText || this.isTyping;
                }
                
                async sendMessage() {
                    const message = this.chatInput.value.trim();
                    if (!message || this.isTyping) return;
                    
                    this.addMessage(message, 'user');
                    this.chatInput.value = '';
                    this.toggleSendButton();
                    this.showTyping();
                    
                    try {
                        const payload = {
                            message: message,
                            sessionId: this.sessionId
                        };
                        
                        console.log('Enviando:', payload);
                        
                        let response;
                        try {
                            response = await fetch(this.webhookUrl, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify(payload),
                                mode: 'cors'
                            });
                        } catch (corsError) {
                            console.log('CORS error, intentando método alternativo:', corsError);
                            
                            const formData = new FormData();
                            formData.append('message', message);
                            formData.append('sessionId', this.sessionId);
                            
                            response = await fetch(this.webhookUrl, {
                                method: 'POST',
                                body: formData,
                                mode: 'no-cors'
                            });
                        }
                        
                        console.log('Response status:', response.status);
                        
                        if (!response.ok) {
                            console.error('HTTP Error:', response.status, response.statusText);
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        
                        const responseText = await response.text();
                        console.log('Response text:', responseText);
                        
                        let data;
                        let botMessage = '';
                        
                        try {
                            data = JSON.parse(responseText);
                            console.log('Parsed JSON:', data);
                            
                            if (data.output) {
                                botMessage = data.output;
                            } else if (data.Output) {
                                botMessage = data.Output;
                            } else if (data.response) {
                                botMessage = data.response;
                            } else if (data.message) {
                                botMessage = data.message;
                            } else if (data.text) {
                                botMessage = data.text;
                            } else if (typeof data === 'string') {
                                botMessage = data;
                            }
                            
                            if (botMessage.startsWith('{') && botMessage.endsWith('}')) {
                                try {
                                    const innerData = JSON.parse(botMessage);
                                    botMessage = innerData.text || innerData.message || innerData.response || botMessage;
                                } catch (e) {
                                    // Si no se puede parsear, usar como está
                                }
                            }
                            
                        } catch (e) {
                            console.log('No es JSON válido, usando como texto:', e);
                            botMessage = responseText;
                        }
                        
                        this.hideTyping();
                        
                        if (botMessage) {
                            // Procesar formato de enlaces [texto](url)
                            botMessage = botMessage.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank" rel="noopener noreferrer" style="color: #01B7AF; text-decoration: underline;">$1</a>');
                            this.addMessage(botMessage, 'bot');
                        } else {
                            this.addMessage('Gracias por tu mensaje. Un especialista de BravesLab te contactará pronto para hablar sobre cómo integrar IA en tu empresa.', 'bot');
                        }
                        
                    } catch (error) {
                        console.error('Error detallado:', error);
                        this.hideTyping();
                        this.addMessage('Hay un problema de conexión. Por favor, contactanos directamente en BravesLab.com o intenta de nuevo en unos segundos.', 'bot');
                    }
                }
                
                addMessage(text, sender) {
                    const messageDiv = document.createElement('div');
                    messageDiv.className = `message ${sender}`;
                    
                    const bubbleDiv = document.createElement('div');
                    bubbleDiv.className = 'message-bubble';
                    bubbleDiv.innerHTML = this.processLinks(text);
                    
                    const timeDiv = document.createElement('div');
                    timeDiv.className = 'message-time';
                    timeDiv.textContent = this.formatTime(new Date());
                    
                    messageDiv.appendChild(bubbleDiv);
                    messageDiv.appendChild(timeDiv);
                    this.chatMessages.appendChild(messageDiv);
                    
                    this.scrollToBottom();
                }
                processLinks(text) {
                    // Escapar HTML primero para seguridad
                    const escaped = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    
                    // Detectar URLs y convertirlas en enlaces
                    const urlRegex = /(https?:\/\/[^\s]+)/g;
                    return escaped.replace(urlRegex, '<a href="$1" target="_blank" rel="noopener noreferrer" style="color: #01B7AF; text-decoration: underline;">$1</a>');
                }

                showTyping() {
                    this.isTyping = true;
                    this.typingIndicator.style.display = 'flex';
                    this.toggleSendButton();
                    this.scrollToBottom();
                }
                
                hideTyping() {
                    this.isTyping = false;
                    this.typingIndicator.style.display = 'none';
                    this.toggleSendButton();
                }
                
                scrollToBottom() {
                    setTimeout(() => {
                        this.chatMessages.scrollTop = this.chatMessages.scrollHeight;
                    }, 100);
                }
                
                formatTime(date) {
                    return date.toLocaleTimeString('es-ES', { 
                        hour: '2-digit', 
                        minute: '2-digit' 
                    });
                }
            }
            
            // Inicializar el chat
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => {
                    new BravesLabChat();
                });
            } else {
                new BravesLabChat();
            }
        })();
        </script>