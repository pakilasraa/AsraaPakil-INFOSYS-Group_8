<x-app-layout>
    <div class="p-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-2xl font-bold mb-4 text-[#3d2b1f]">AI Assistant</h2>
                <p class="mb-4 text-gray-600">Welcome! I can help you manage sales, inventory, and promotions.</p>

                <!-- Suggested Questions -->
                <div class="mb-4 flex flex-wrap gap-2">
                    <button onclick="fillAndSend('How much are sales today?')" class="px-3 py-1.5 rounded-full border border-[#3d2b1f] text-[#3d2b1f] text-sm hover:bg-[#3d2b1f] hover:text-white transition-colors">
                        📈 Sales Today?
                    </button>
                    <button onclick="fillAndSend('What are the best selling items?')" class="px-3 py-1.5 rounded-full border border-[#3d2b1f] text-[#3d2b1f] text-sm hover:bg-[#3d2b1f] hover:text-white transition-colors">
                        🏆 Best Sellers?
                    </button>
                    <button onclick="fillAndSend('What items are low on stock?')" class="px-3 py-1.5 rounded-full border border-[#3d2b1f] text-[#3d2b1f] text-sm hover:bg-[#3d2b1f] hover:text-white transition-colors">
                        ⚠️ Low Stock?
                    </button>
                    <button onclick="fillAndSend('Suggest a promo for this afternoon')" class="px-3 py-1.5 rounded-full border border-[#3d2b1f] text-[#3d2b1f] text-sm hover:bg-[#3d2b1f] hover:text-white transition-colors">
                        💡 Promo Idea
                    </button>
                </div>

                <div class="border rounded-lg p-4 h-96 overflow-y-auto mb-4 bg-[#faf5ef]" id="chat-window">
                    @if(empty($history))
                        <div class="text-center text-gray-400 mt-32">
                            <div class="text-4xl mb-2">☕</div>
                            Start a conversation...
                        </div>
                    @else
                        @foreach($history as $msg)
                            <div class="text-{{ $msg['role'] === 'user' ? 'right' : 'left' }} mb-3">
                                <span class="px-4 py-2 rounded-xl inline-block shadow-sm max-w-[80%] {{ $msg['role'] === 'user' ? 'bg-[#3d2b1f] text-white rounded-br-none' : 'bg-white text-gray-800 border border-gray-100 rounded-bl-none' }}">
                                    {!! nl2br(e($msg['content'])) !!}
                                </span>
                            </div>
                        @endforeach
                    @endif
                </div>

                <div class="flex gap-2">
                    <input type="text" id="user-input" class="w-full border-gray-300 rounded-md shadow-sm focus:border-[#3d2b1f] focus:ring-[#3d2b1f]" placeholder="Type your message...">
                    <button id="send-btn" class="bg-[#3d2b1f] text-white px-6 py-2 rounded-md hover:bg-[#2a1e16] transition-colors font-medium">Send</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Script -->
    <script>
        document.getElementById('send-btn').addEventListener('click', sendMessage);
        document.getElementById('user-input').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') sendMessage();
        });

        // Auto-scroll to bottom on load
        window.onload = function() {
            const chatWindow = document.getElementById('chat-window');
            chatWindow.scrollTop = chatWindow.scrollHeight;
        };

        function fillAndSend(question) {
            const input = document.getElementById('user-input');
            input.value = question;
            sendMessage();
        }

        async function sendMessage() {
            const input = document.getElementById('user-input');
            const message = input.value.trim();
            if(!message) return;

            const chatWindow = document.getElementById('chat-window');

            // Remove empty state if present
            if (chatWindow.querySelector('.text-center.text-gray-400')) {
                chatWindow.innerHTML = '';
            }

            // Append User Message
            const userDiv = document.createElement('div');
            userDiv.className = 'text-right mb-3';
            userDiv.innerHTML = `<span class="bg-[#3d2b1f] text-white px-4 py-2 rounded-xl inline-block shadow-sm rounded-br-none max-w-[80%]">${escapeHtml(message)}</span>`;
            chatWindow.appendChild(userDiv);

            input.value = '';
            chatWindow.scrollTop = chatWindow.scrollHeight;

            // Loading indicator
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'text-left mb-3';
            loadingDiv.innerHTML = `<span class="bg-white text-gray-500 border border-gray-200 px-4 py-2 rounded-xl inline-block rounded-bl-none shadow-sm text-sm">Thinking... ☕</span>`;
            chatWindow.appendChild(loadingDiv);
            chatWindow.scrollTop = chatWindow.scrollHeight;

            try {
                const response = await fetch('{{ route('ai.admin-chat') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ message: message })
                });

                const data = await response.json();

                // Remove loading
                chatWindow.removeChild(loadingDiv);

                // Append AI Message
                const aiDiv = document.createElement('div');
                aiDiv.className = 'text-left mb-3';
                aiDiv.innerHTML = `<span class="bg-white text-gray-800 border border-gray-100 px-4 py-2 rounded-xl inline-block rounded-bl-none shadow-sm max-w-[80%]">${escapeHtml(data.message || 'Error getting response').replace(/\n/g, '<br>')}</span>`;
                chatWindow.appendChild(aiDiv);

            } catch (error) {
                chatWindow.removeChild(loadingDiv);
                const errorDiv = document.createElement('div');
                errorDiv.className = 'text-left mb-3';
                errorDiv.innerHTML = `<span class="bg-red-50 text-red-600 border border-red-200 px-4 py-2 rounded-xl inline-block rounded-bl-none shadow-sm text-sm">Error: ${error.message}</span>`;
                chatWindow.appendChild(errorDiv);
            }

            chatWindow.scrollTop = chatWindow.scrollHeight;
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
    </script>
</x-app-layout>
