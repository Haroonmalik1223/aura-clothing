<div x-data="auraChatbot()" class="relative z-50 font-sans" x-cloak>

    {{-- ======================================================== --}}
    {{-- FLOATING CHAT CIRCLE (BOTTOM RIGHT) --}}
    {{-- ======================================================== --}}
    <div class="fixed bottom-6 right-6 z-50">
        <button @click="toggleChat()"
                class="w-14 h-14 rounded-full bg-black text-white shadow-2xl flex items-center justify-center hover:scale-105 active:scale-95 transition-all duration-300 relative border border-gray-700 focus:outline-none group"
                aria-label="Open AI Store Assistant">

            {{-- Online pulsing indicator --}}
            <span class="absolute top-0 right-0 flex h-3.5 w-3.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-emerald-500 border-2 border-white"></span>
            </span>

            {{-- Chat icon when closed --}}
            <svg x-show="!isOpen" class="w-6 h-6 transition-transform duration-300 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
            </svg>

            {{-- Close 'X' icon when open --}}
            <svg x-show="isOpen" class="w-6 h-6 transition-transform duration-300 rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- ======================================================== --}}
    {{-- CHAT WINDOW POPUP --}}
    {{-- ======================================================== --}}
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-6 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-6 scale-95"
         class="fixed bottom-24 right-4 sm:right-6 z-50 w-[calc(100vw-2rem)] sm:w-[410px] h-[580px] max-h-[85vh] bg-white border border-gray-200 shadow-2xl rounded-2xl flex flex-col overflow-hidden">

        {{-- Chat Header --}}
        <div class="bg-black text-white px-5 py-4 flex items-center justify-between border-b border-gray-800 flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-white text-black font-serif font-bold text-lg flex items-center justify-center rounded-lg shadow-sm">
                    A
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="font-serif font-bold text-sm tracking-wide text-white">AURA Stylist & Support</h3>
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    </div>
                    <p class="text-[10px] text-gray-400 uppercase tracking-widest font-mono">Powered by Gemini AI</p>
                </div>
            </div>

            <div class="flex items-center gap-1 text-gray-400">
                {{-- Clear chat --}}
                <button @click="clearChat()" title="Reset conversation" class="p-1.5 hover:text-white hover:bg-gray-800 rounded transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </button>
                {{-- Minimize --}}
                <button @click="closeChat()" title="Close" class="p-1.5 hover:text-white hover:bg-gray-800 rounded transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Message Stream --}}
        <div x-ref="messagesContainer" class="flex-grow overflow-y-auto p-4 space-y-2.5 bg-gray-50/60 text-xs">

            {{-- Welcome Note --}}
            <div class="bg-white border border-gray-200 p-4 rounded-xl shadow-xs space-y-2.5">
                <p class="font-semibold text-gray-900">Welcome to AURA Atelier.</p>
                <p class="text-gray-600 leading-relaxed">
                    I am your AI Personal Stylist. I can assist you with real-time product availability, sizing, outfit recommendations, and store policies across Pakistan.
                </p>

                {{-- Quick FAQ / Action Chips --}}
                <div class="pt-1 flex flex-wrap gap-1.5">
                    <button type="button" @click="sendQuick('Do you offer Cash on Delivery (COD)?')" class="bg-gray-100 hover:bg-black hover:text-white text-gray-800 px-2.5 py-1 rounded-full text-[10px] font-medium transition border border-gray-200">
                        💳 Cash on Delivery
                    </button>
                    <button type="button" @click="sendQuick('What are the shipping charges and delivery time?')" class="bg-gray-100 hover:bg-black hover:text-white text-gray-800 px-2.5 py-1 rounded-full text-[10px] font-medium transition border border-gray-200">
                        🚚 Shipping & Timeline
                    </button>
                    <button type="button" @click="sendQuick('What is your return and exchange policy?')" class="bg-gray-100 hover:bg-black hover:text-white text-gray-800 px-2.5 py-1 rounded-full text-[10px] font-medium transition border border-gray-200">
                        🔄 30-Day Returns
                    </button>
                    <button type="button" @click="sendQuick('Show me your best hoodies currently in stock')" class="bg-gray-100 hover:bg-black hover:text-white text-gray-800 px-2.5 py-1 rounded-full text-[10px] font-medium transition border border-gray-200">
                        🧥 In-Stock Hoodies
                    </button>
                    <button type="button" @click="sendQuick('What sizes do you offer and how does sizing fit?')" class="bg-gray-100 hover:bg-black hover:text-white text-gray-800 px-2.5 py-1 rounded-full text-[10px] font-medium transition border border-gray-200">
                        📏 Size Guide
                    </button>
                </div>
            </div>

            {{-- Chat History Bubbles --}}
            <template x-for="(msg, index) in messages" :key="index">
                <div class="flex w-full" :class="msg.role === 'user' ? 'justify-end' : 'justify-start'">
                    {{-- User Message --}}
                    <template x-if="msg.role === 'user'">
                        <div class="inline-block max-w-[80%] bg-black text-white text-left rounded-2xl rounded-tr-none px-3.5 py-1.5 text-xs break-words leading-relaxed" x-text="msg.text"></div>
                    </template>

                    {{-- Assistant Message --}}
                    <template x-if="msg.role === 'assistant'">
                        <div class="inline-block max-w-[85%] bg-white border border-gray-200 text-gray-800 rounded-2xl rounded-tl-none px-3.5 py-2 shadow-xs leading-relaxed space-y-1 break-words">
                            <div class="prose prose-xs text-xs text-gray-800 break-words" x-html="formatResponse(msg.text)"></div>
                            <span class="block text-[8px] text-gray-400 font-mono text-right" x-text="msg.time"></span>
                        </div>
                    </template>
                </div>
            </template>

            {{-- Typing Indicator (Only 3 animated dots) --}}
            <div x-show="isLoading" class="flex items-center gap-1.5 bg-white border border-gray-200 px-3.5 py-2.5 rounded-2xl rounded-tl-none w-fit shadow-xs">
                <span class="w-1.5 h-1.5 rounded-full bg-gray-600 animate-bounce" style="animation-delay: 0ms"></span>
                <span class="w-1.5 h-1.5 rounded-full bg-gray-600 animate-bounce" style="animation-delay: 150ms"></span>
                <span class="w-1.5 h-1.5 rounded-full bg-gray-600 animate-bounce" style="animation-delay: 300ms"></span>
            </div>

        </div>

        {{-- Input Footer --}}
        <div class="p-3 bg-white border-t border-gray-200 flex-shrink-0">
            <form @submit.prevent="sendMessage()" class="flex items-center gap-2">
                <input type="text"
                       x-model="inputMessage"
                       :disabled="isLoading"
                       placeholder="Ask about clothing, sizes, policies..."
                       class="flex-grow bg-gray-50 border border-gray-300 text-xs px-3.5 py-2.5 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:border-black focus:bg-white transition disabled:opacity-50">

                <button type="submit"
                        :disabled="isLoading || !inputMessage.trim()"
                        class="bg-black hover:bg-gray-800 text-white w-9 h-9 rounded-xl flex items-center justify-center transition disabled:opacity-30 disabled:cursor-not-allowed flex-shrink-0"
                        title="Send message">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </form>
            <div class="text-[9px] text-gray-400 text-center mt-1.5 font-medium">
                Real-time stock & fashion guidance • PKR Pricing
            </div>
        </div>

    </div>

</div>

@push('scripts')
<script>
function auraChatbot() {
    return {
        isOpen: false,
        isLoading: false,
        inputMessage: '',
        messages: [],

        init() {
            // Remove legacy open-state key so chat never opens automatically
            try {
                sessionStorage.removeItem('aura_chat_open');
            } catch (e) {}

            // Restore chat history from user's browser session (window stays closed by default)
            try {
                const savedMessages = sessionStorage.getItem('aura_chat_messages');
                if (savedMessages) {
                    const parsed = JSON.parse(savedMessages);
                    if (Array.isArray(parsed)) {
                        this.messages = parsed;
                    }
                }
            } catch (e) {
                console.warn('Session storage read error:', e);
            }
        },

        toggleChat() {
            this.isOpen = !this.isOpen;
            if (this.isOpen && typeof this.$nextTick === 'function') {
                this.$nextTick(() => this.scrollToBottom());
            }
        },

        closeChat() {
            this.isOpen = false;
        },

        saveSession() {
            try {
                sessionStorage.setItem('aura_chat_messages', JSON.stringify(this.messages));
            } catch (e) {
                console.warn('Session storage write error:', e);
            }
        },

        sendQuick(promptText) {
            this.inputMessage = promptText;
            this.sendMessage();
        },

        async sendMessage() {
            const text = this.inputMessage.trim();
            if (!text || this.isLoading) return;

            const now = new Date();
            const timeStr = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

            // Add user message
            this.messages.push({
                role: 'user',
                text: text,
                time: timeStr
            });
            this.saveSession();

            this.inputMessage = '';
            this.isLoading = true;
            this.$nextTick(() => this.scrollToBottom());

            // Prepare history for multi-turn Gemini conversation
            const historyPayload = this.messages.slice(-10).map(m => ({
                role: m.role === 'user' ? 'user' : 'model',
                text: m.text
            }));

            try {
                const response = await fetch('/chat/message', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        message: text,
                        history: historyPayload
                    })
                });

                const data = await response.json();
                const replyText = data.reply || "I am currently unable to fetch a response. Please feel free to browse our collections or contact our support team.";

                this.messages.push({
                    role: 'assistant',
                    text: replyText,
                    time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                });
                this.saveSession();

            } catch (error) {
                console.error("Chat error:", error);
                this.messages.push({
                    role: 'assistant',
                    text: "Sorry, I encountered a network issue. Please check your connection and try again.",
                    time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                });
                this.saveSession();
            } finally {
                this.isLoading = false;
                this.$nextTick(() => this.scrollToBottom());
            }
        },

        clearChat() {
            this.messages = [];
            try {
                sessionStorage.removeItem('aura_chat_messages');
            } catch (e) {}
        },

        scrollToBottom() {
            const container = this.$refs.messagesContainer;
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        },

        formatResponse(rawText) {
            if (!rawText) return '';

            // Sanitize raw HTML tags to prevent XSS
            let safe = rawText.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');

            // 1. Convert markdown bold **text** to <strong>
            let formatted = safe.replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold text-black">$1</strong>');

            // 2. Convert markdown links [Text](/product/slug) or [Text](url) to styled buttons/links
            formatted = formatted.replace(/\[(.*?)\]\((.*?)\)/g, function(match, label, url) {
                const safeUrl = url.replace(/"/g, '&quot;');
                return `<a href="${safeUrl}" class="inline-flex items-center gap-1 font-semibold text-black bg-gray-100 hover:bg-black hover:text-white px-2 py-0.5 rounded transition text-[11px] underline my-0.5">${label} &rarr;</a>`;
            });

            // 3. Convert bullet points
            formatted = formatted.replace(/^\s*[-•*]\s+(.*)$/gm, '<li class="ml-3 list-disc">$1</li>');

            // 4. Convert newlines to breaks
            formatted = formatted.replace(/\n\n/g, '<br><br>').replace(/\n/g, '<br>');

            return formatted;
        }
    };
}
</script>
@endpush
