<x-app-layout>
    <div class="p-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-2xl font-bold mb-4">AI Assistant</h2>
                <p class="mb-4">Welcome to the AI Assistant. How can I help you managing the cafe today?</p>
                
                <div class="border rounded-lg p-4 h-96 overflow-y-auto mb-4 bg-gray-50" id="chat-window">
                    <!-- Chat history will appear here -->
                    <div class="text-center text-gray-500 mt-20">
                        Start a conversation...
                    </div>
                </div>

                <div class="flex gap-2">
                    <input type="text" id="user-input" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Type your message...">
                    <button id="send-btn" class="bg-[#3d2b1f] text-white px-4 py-2 rounded-md hover:bg-[#2a1e16]">Send</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Simple script for handling chat -->
    <script>
        document.getElementById('send-btn').addEventListener('click', sendMessage);
        document.getElementById('user-input').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') sendMessage();
        });

        async function sendMessage() {
            const input = document.getElementById('user-input');
            const message = input.value;
            if(!message) return;

            const chatWindow = document.getElementById('chat-window');
            
            // Append User Message
            const userDiv = document.createElement('div');
            userDiv.className = 'text-right mb-2';
            userDiv.innerHTML = `<span class="bg-[#3d2b1f] text-white px-3 py-2 rounded-lg inline-block">${escapeHtml(message)}</span>`;
            chatWindow.appendChild(userDiv);
            
            input.value = '';
            chatWindow.scrollTop = chatWindow.scrollHeight;

            // Loading indicator
            const loadingDiv = document.createElement('div');
            loadingDiv.className = 'text-left mb-2';
            loadingDiv.innerHTML = `<span class="bg-gray-200 text-gray-800 px-3 py-2 rounded-lg inline-block">Thinking...</span>`;
            chatWindow.appendChild(loadingDiv);

            try {
                const response = await fetch('{{ route('ai.admin-chat') }}', { // Ensure route name matches api.php
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
                aiDiv.className = 'text-left mb-2';
                aiDiv.innerHTML = `<span class="bg-gray-200 text-gray-800 px-3 py-2 rounded-lg inline-block">${escapeHtml(data.message || 'Error getting response')}</span>`;
                chatWindow.appendChild(aiDiv);
                
            } catch (error) {
                chatWindow.removeChild(loadingDiv);
                const errorDiv = document.createElement('div');
                errorDiv.className = 'text-left mb-2';
                errorDiv.innerHTML = `<span class="bg-red-200 text-red-800 px-3 py-2 rounded-lg inline-block">Error: ${error.message}</span>`;
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
