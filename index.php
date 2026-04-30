<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Media Scheduler - Monthly Plan</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>📅</text></svg>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .calendar-day { min-height: 120px; transition: all 0.2s; }
        .calendar-day:hover { transform: scale(1.02); }
        .has-content { background: linear-gradient(135deg, #e0f2fe 0%, #dbeafe 100%); }
        .loader {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3b82f6;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .prompt-box { position: relative; }
        .prompt-box .copy-btn { position: absolute; top: 8px; right: 8px; opacity: 0; transition: opacity 0.2s; }
        .prompt-box:hover .copy-btn { opacity: 1; }
        .copy-btn.copied { background-color: #10b981 !important; color: white !important; }
        .prompt-tabs .prompt-tab { cursor: pointer; transition: all 0.2s; }
        .prompt-tabs .prompt-tab.active { background-color: #7c3aed; color: white; }
        .prompt-tabs .prompt-tab:not(.active) { background-color: #f3f4f6; color: #374151; }
        .prompt-tabs .prompt-tab:not(.active):hover { background-color: #e5e7eb; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen p-4 md:p-8">

    <div class="max-w-7xl mx-auto">
        <?php 
        require_once 'config.php';
        include 'navbar.php'; 
        ?>

        <!-- Header -->
        <header class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Social Media Scheduler</h1>
                <p class="text-gray-500">AI-Powered Monthly Content Planning</p>
            </div>
            <div class="flex gap-3 items-center">
                <div id="ollamaStatus" class="flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100">
                    <div class="w-3 h-3 rounded-full bg-gray-400" id="ollamaIndicator"></div>
                    <span class="text-sm text-gray-600" id="ollamaStatusText">Checking...</span>
                </div>
                <select id="monthSelect" class="px-4 py-2 border border-gray-300 rounded-lg bg-white">
                    <option value="0">January</option>
                    <option value="1">February</option>
                    <option value="2">March</option>
                    <option value="3">April</option>
                    <option value="4">May</option>
                    <option value="5">June</option>
                    <option value="6">July</option>
                    <option value="7">August</option>
                    <option value="8">September</option>
                    <option value="9">October</option>
                    <option value="10">November</option>
                    <option value="11">December</option>
                </select>
                <select id="yearSelect" class="px-4 py-2 border border-gray-300 rounded-lg bg-white">
                    <option value="2024">2024</option>
                    <option value="2025" selected>2025</option>
                    <option value="2026">2026</option>
                </select>
                <button id="generateBtn" onclick="generateMonthlyPlan()" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg flex items-center gap-2 transition-all">
                    <i class="fas fa-magic"></i>
                    <span>Generate Monthly Plan</span>
                    <div id="btnLoader" class="loader hidden"></div>
                </button>
            </div>
        </header>

        <!-- Status Box -->
        <div id="statusBox" class="hidden mb-6 p-4 bg-blue-50 border-l-4 border-blue-600 rounded-lg">
            <p id="statusText" class="text-sm font-semibold text-blue-800"></p>
        </div>

        <!-- Real-time Progress Display -->
        <div id="progressBox" class="hidden mb-6 bg-gray-900 rounded-lg p-4 text-green-400 font-mono text-sm">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                <span class="text-green-300 font-semibold">AI Generation Progress</span>
            </div>
            <div id="progressOutput" class="space-y-1 max-h-64 overflow-y-auto">
                <!-- Streaming output will appear here -->
            </div>
        </div>

        <!-- Platform Filter Tabs -->
        <div class="bg-white rounded-2xl shadow-lg p-4 mb-4">
            <div class="flex flex-wrap gap-2">
                <button onclick="filterByPlatform('all')" class="platform-tab px-4 py-2 rounded-lg font-medium transition-all bg-blue-600 text-white" data-platform="all">
                    All Platforms
                </button>
                <button onclick="filterByPlatform('Instagram')" class="platform-tab px-4 py-2 rounded-lg font-medium transition-all bg-gray-100 hover:bg-gray-200 text-gray-700" data-platform="Instagram">
                    <i class="fab fa-instagram mr-2"></i>Instagram
                </button>
                <button onclick="filterByPlatform('Facebook')" class="platform-tab px-4 py-2 rounded-lg font-medium transition-all bg-gray-100 hover:bg-gray-200 text-gray-700" data-platform="Facebook">
                    <i class="fab fa-facebook mr-2"></i>Facebook
                </button>
                <button onclick="filterByPlatform('Twitter')" class="platform-tab px-4 py-2 rounded-lg font-medium transition-all bg-gray-100 hover:bg-gray-200 text-gray-700" data-platform="Twitter">
                    <i class="fab fa-twitter mr-2"></i>Twitter
                </button>
                <button onclick="filterByPlatform('LinkedIn')" class="platform-tab px-4 py-2 rounded-lg font-medium transition-all bg-gray-100 hover:bg-gray-200 text-gray-700" data-platform="LinkedIn">
                    <i class="fab fa-linkedin mr-2"></i>LinkedIn
                </button>
            </div>
        </div>

        <!-- Calendar -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <!-- Weekday Headers -->
            <div class="grid grid-cols-7 gap-2 mb-4">
                <div class="text-center font-semibold text-gray-600 py-2">Sun</div>
                <div class="text-center font-semibold text-gray-600 py-2">Mon</div>
                <div class="text-center font-semibold text-gray-600 py-2">Tue</div>
                <div class="text-center font-semibold text-gray-600 py-2">Wed</div>
                <div class="text-center font-semibold text-gray-600 py-2">Thu</div>
                <div class="text-center font-semibold text-gray-600 py-2">Fri</div>
                <div class="text-center font-semibold text-gray-600 py-2">Sat</div>
            </div>

            <!-- Calendar Grid -->
            <div id="calendarGrid" class="grid grid-cols-7 gap-2">
                <!-- Days will be generated by JavaScript -->
            </div>
        </div>

        <!-- Stats -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <div class="text-sm text-gray-500">Total Posts</div>
                <div id="totalPosts" class="text-2xl font-bold text-gray-900">0</div>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <div class="text-sm text-gray-500">Scheduled</div>
                <div id="scheduledPosts" class="text-2xl font-bold text-green-600">0</div>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <div class="text-sm text-gray-500">Pending</div>
                <div id="pendingPosts" class="text-2xl font-bold text-orange-600">0</div>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <div class="text-sm text-gray-500">Completion</div>
                <div id="completionRate" class="text-2xl font-bold text-blue-600">0%</div>
            </div>
        </div>
    </div>

    <!-- Editorial Detail Modal -->
    <div id="editorialModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-200 p-6 flex justify-between items-center">
                <h3 id="modalTitle" class="text-xl font-bold text-gray-900"></h3>
                <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
            </div>
            <div id="modalContent" class="p-6 space-y-6">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>
    </div>

    <script>
        const OLLAMA_API = "<?php echo OLLAMA_API_URL; ?>";
        const MODEL = "<?php echo OLLAMA_MODEL; ?>";
        const COMPANY_NAME = "<?php echo COMPANY_NAME; ?>";
        const COMPANY_WEBSITE = "<?php echo COMPANY_WEBSITE; ?>";
        const COMPANY_INDUSTRY = "<?php echo COMPANY_INDUSTRY; ?>";
        const COMPANY_DESCRIPTION = "<?php echo COMPANY_DESCRIPTION; ?>";
        const COMPANY_TARGET_AUDIENCE = "<?php echo COMPANY_TARGET_AUDIENCE; ?>";
        const COMPANY_TONE = "<?php echo COMPANY_TONE; ?>";
        const COMPANY_KEY_PRODUCTS = "<?php echo COMPANY_KEY_PRODUCTS; ?>";
        const COMPANY_VALUES = "<?php echo COMPANY_VALUES; ?>";
        let monthlyPlan = {};
        let currentMonth = new Date().getMonth();
        let currentYear = new Date().getFullYear();
        let currentPlatformFilter = 'all';

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('monthSelect').value = currentMonth;
            document.getElementById('yearSelect').value = currentYear;
            renderCalendar();
            loadSavedPlan();
            checkOllamaStatus();
        });

        // Check Ollama status on page load
        async function checkOllamaStatus() {
            const indicator = document.getElementById('ollamaIndicator');
            const statusText = document.getElementById('ollamaStatusText');
            const statusBox = document.getElementById('ollamaStatus');

            try {
                const isRunning = await checkOllamaConnection();
                if (isRunning) {
                    indicator.className = 'w-3 h-3 rounded-full bg-green-500';
                    statusText.textContent = 'Ollama Connected';
                    statusText.className = 'text-sm text-green-600';
                    statusBox.className = 'flex items-center gap-2 px-4 py-2 rounded-lg bg-green-50';
                } else {
                    indicator.className = 'w-3 h-3 rounded-full bg-red-500';
                    statusText.textContent = 'Ollama Not Running';
                    statusText.className = 'text-sm text-red-600';
                    statusBox.className = 'flex items-center gap-2 px-4 py-2 rounded-lg bg-red-50';
                }
            } catch (error) {
                indicator.className = 'w-3 h-3 rounded-full bg-red-500';
                statusText.textContent = 'Ollama Error';
                statusText.className = 'text-sm text-red-600';
                statusBox.className = 'flex items-center gap-2 px-4 py-2 rounded-lg bg-red-50';
            }
        }

        document.getElementById('monthSelect').addEventListener('change', (e) => {
            currentMonth = parseInt(e.target.value);
            renderCalendar();
            loadSavedPlan();
        });

        document.getElementById('yearSelect').addEventListener('change', (e) => {
            currentYear = parseInt(e.target.value);
            renderCalendar();
            loadSavedPlan();
        });

        function filterByPlatform(platform) {
            currentPlatformFilter = platform;
            
            // Update tab styling
            document.querySelectorAll('.platform-tab').forEach(tab => {
                if (tab.dataset.platform === platform) {
                    tab.className = 'platform-tab px-4 py-2 rounded-lg font-medium transition-all bg-blue-600 text-white';
                } else {
                    tab.className = 'platform-tab px-4 py-2 rounded-lg font-medium transition-all bg-gray-100 hover:bg-gray-200 text-gray-700';
                }
            });
            
            renderCalendar();
        }

        function getDaysInMonth(month, year) {
            return new Date(year, month + 1, 0).getDate();
        }

        function getFirstDayOfMonth(month, year) {
            return new Date(year, month, 1).getDay();
        }

        function renderCalendar() {
            const grid = document.getElementById('calendarGrid');
            grid.innerHTML = '';

            const daysInMonth = getDaysInMonth(currentMonth, currentYear);
            const firstDay = getFirstDayOfMonth(currentMonth, currentYear);
            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

            // Empty cells for days before first day
            for (let i = 0; i < firstDay; i++) {
                const emptyCell = document.createElement('div');
                emptyCell.className = 'calendar-day bg-gray-50 rounded-lg';
                grid.appendChild(emptyCell);
            }

            // Days of the month
            for (let day = 1; day <= daysInMonth; day++) {
                const dayCell = document.createElement('div');
                const dateKey = `${currentYear}-${currentMonth + 1}-${day}`;
                const content = monthlyPlan[dateKey];
                
                // Filter by platform
                const hasContent = content && (currentPlatformFilter === 'all' || content.platform === currentPlatformFilter);

                dayCell.className = `calendar-day ${hasContent ? 'has-content cursor-pointer' : 'bg-white border border-gray-100'} rounded-lg p-2`;
                dayCell.innerHTML = `
                    <div class="font-semibold text-gray-900 mb-1">${day}</div>
                    ${hasContent ? `
                        <div class="text-xs text-blue-600 font-medium truncate">${content.topic}</div>
                        <div class="text-xs text-gray-500 truncate">${content.platform}</div>
                        <div class="mt-1 flex gap-1 items-center">
                            ${content.status === 'scheduled' ? '<span class="w-2 h-2 bg-green-500 rounded-full"></span>' : '<span class="w-2 h-2 bg-orange-500 rounded-full"></span>'}
                            ${content.imagePrompt ? '<span class="text-xs text-purple-500 ml-1" title="Image prompt available"><i class="fas fa-paint-brush"></i></span>' : ''}
                        </div>
                    ` : '<div class="text-xs text-gray-400">No content</div>'}
                `;

                if (hasContent) {
                    dayCell.onclick = () => openEditorialModal(dateKey);
                }

                grid.appendChild(dayCell);
            }

            updateStats();
        }

        // Check if Ollama is running
        async function checkOllamaConnection() {
            try {
                const response = await fetch(`${OLLAMA_API}/tags`);
                return response.ok;
            } catch (error) {
                return false;
            }
        }

        // Ollama API call function with streaming using fetch
        async function callOllama(prompt, system = "", onProgress = null) {
            const progressOutput = document.getElementById('progressOutput');
            let fullResponse = '';
            
            // Add task indicator
            const taskDiv = document.createElement('div');
            taskDiv.className = 'text-yellow-400';
            taskDiv.innerHTML = `<span class="animate-pulse">&#9654;</span> ${prompt.substring(0, 50)}...`;
            progressOutput.appendChild(taskDiv);
            progressOutput.scrollTop = progressOutput.scrollHeight;

            try {
                const response = await fetch(`chat.php?prompt=${encodeURIComponent(prompt)}&system=${encodeURIComponent(system)}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const reader = response.body.getReader();
                const decoder = new TextDecoder();
                let buffer = '';

                while (true) {
                    const { done, value } = await reader.read();
                    if (done) break;

                    buffer += decoder.decode(value, { stream: true });
                    const lines = buffer.split('\n\n');
                    buffer = lines.pop(); // Keep the last incomplete line in buffer

                    for (const line of lines) {
                        if (line.startsWith('data: ')) {
                            try {
                                const data = JSON.parse(line.substring(6));
                                if (data.response) {
                                    fullResponse += data.response;
                                    if (onProgress) {
                                        onProgress(data.response);
                                    }
                                    // Update progress display
                                    taskDiv.innerHTML = `<span class="text-green-400">&#10003;</span> ${prompt.substring(0, 50)}...`;
                                    taskDiv.className = 'text-green-400';
                                    progressOutput.scrollTop = progressOutput.scrollHeight;
                                }
                                if (data.done) {
                                    taskDiv.innerHTML = `<span class="text-green-400">&#10003;</span> ${prompt.substring(0, 50)}... (completed)`;
                                }
                            } catch (e) {
                                // Skip invalid JSON
                            }
                        }
                    }
                }

                // Process any remaining buffer
                if (buffer.trim()) {
                    const lines = buffer.split('\n');
                    for (const line of lines) {
                        if (line.startsWith('data: ')) {
                            try {
                                const data = JSON.parse(line.substring(6));
                                if (data.response) {
                                    fullResponse += data.response;
                                }
                            } catch (e) {
                                // Skip parsing errors
                            }
                        }
                    }
                }

                // Log the response for debugging
                console.log('callOllama response type:', typeof fullResponse, 'length:', fullResponse.length);
                
                // Ensure we return a string, not a Promise
                const result = String(fullResponse || '');
                console.log('Returning from callOllama, type:', typeof result);
                return result;
            } catch (error) {
                taskDiv.innerHTML = `<span class="text-red-400">&#10007;</span> ${prompt.substring(0, 50)}... (failed: ${error.message})`;
                taskDiv.className = 'text-red-400';
                console.error('callOllama error:', error);
                throw error;
            }
        }

        // Parallel AI search - generate multiple aspects simultaneously
        async function parallelAISearch(monthName, year, daysInMonth) {
            updateStatus("Running parallel AI search...");

            // Platform-specific prompt
            const platformPrompt = currentPlatformFilter === 'all' 
                ? `across Instagram, LinkedIn, Twitter, Facebook`
                : `for ${currentPlatformFilter} only`;

            // Task 1: Generate topics and dates (daily posts for entire month)
            const topicsTask = callOllama(
                `Task: Create a JSON array of ${daysInMonth} consecutive numbers from 1 to ${daysInMonth}.

Example output for 30 days:
[1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30]

Rules:
- Return ONLY valid JSON array
- Must contain exactly ${daysInMonth} numbers
- No additional text or explanation`,
                "You are a JSON generator. Return only valid JSON arrays."
            );

            // Task 2: Generate topic categories (for all days)
            const categoriesTask = callOllama(
                `Task: Generate ${daysInMonth} social media topic names for ${COMPANY_NAME} (${COMPANY_WEBSITE}).

Company Context:
- Industry: ${COMPANY_INDUSTRY}
- Products: ${COMPANY_KEY_PRODUCTS}
- Audience: ${COMPANY_TARGET_AUDIENCE}

Topic Ideas: Product highlights, Safety tips, Installation guides, Quality certifications, Customer testimonials, Technical specifications, Sustainability initiatives, Industry standards, Project showcases, Innovation updates

Example output format:
["Product Highlights", "Safety Tips", "Installation Guides", "Quality Certifications", ...]

Rules:
- Return ONLY a valid JSON array of strings
- Must contain exactly ${daysInMonth} unique topic names
- Each topic must be a simple string (NOT an object)
- No additional text or explanation`,
                "You are a content strategist. Return only valid JSON arrays of topic strings."
            );

            // Task 3: Generate platform mix (for all days)
            const platformsTask = callOllama(
                `Task: Generate ${daysInMonth} social media platform names.

${currentPlatformFilter !== 'all' 
    ? `Platform Requirement: Use ONLY "${currentPlatformFilter}" for all ${daysInMonth} items.

Example output:
["${currentPlatformFilter}", "${currentPlatformFilter}", "${currentPlatformFilter}", "${currentPlatformFilter}", "${currentPlatformFilter}"]
(repeat "${currentPlatformFilter}" exactly ${daysInMonth} times)`
    : `Available Platforms: Instagram, LinkedIn, Twitter, Facebook

Example output:
["Instagram", "LinkedIn", "Twitter", "Facebook", "Instagram", "LinkedIn", ...]
(mix of platforms, total ${daysInMonth} items)`}

Rules:
- Return ONLY a valid JSON array of strings
- Must contain exactly ${daysInMonth} platform names
- Each platform must be a simple string (NOT an object)
- No additional text or explanation`,
                "You are a social media planner. Return only valid JSON arrays of platform strings."
            );

            // Task 4: Generate content types (for all days)
            const contentTypesTask = callOllama(
                `Task: Generate ${daysInMonth} social media content type names.

Available Content Types:
- Carousel Post (multiple images/slides)
- Single Image (one photo)
- Video Story (short video)
- Text Post (text only)
- Reel (short-form video)
- Poll (interactive question)
- Infographic (data visualization)

${currentPlatformFilter === 'Instagram' ? 'Focus on: Carousel Post, Reel, Single Image, Video Story' : ''}
${currentPlatformFilter === 'LinkedIn' ? 'Focus on: Carousel Post, Single Image, Text Post, Video' : ''}
${currentPlatformFilter === 'Twitter' ? 'Focus on: Text Post, Single Image, Poll, Video' : ''}
${currentPlatformFilter === 'Facebook' ? 'Focus on: Video Story, Carousel Post, Single Image, Text Post' : ''}

Example output:
["Carousel Post", "Single Image", "Video Story", "Text Post", "Reel", "Poll", ...]

Rules:
- Return ONLY a valid JSON array of strings
- Must contain exactly ${daysInMonth} content type names
- Each type must be a simple string (NOT an object)
- Vary the content types for diversity
- No additional text or explanation`,
                "You are a content creator. Return only valid JSON arrays of content type strings."
            );

            // Wait for all parallel tasks to complete
            const [topics, categories, platforms, contentTypes] = await Promise.all([
                topicsTask,
                categoriesTask,
                platformsTask,
                contentTypesTask
            ]);

            // Debug: Check types
            console.log('topics type:', typeof topics);
            console.log('categories type:', typeof categories);
            console.log('platforms type:', typeof platforms);
            console.log('contentTypes type:', typeof contentTypes);

            // Parse responses with validation
            function cleanJson(str) {
                console.log('cleanJson input type:', typeof str, 'value:', str);
                
                // Convert to string if it's not already
                str = String(str);
                
                // Remove markdown code blocks
                let cleaned = str.replace(/```json|```/g, "").trim();
                
                // Fix multi-line arrays that are split incorrectly (e.g., [1,2,3]\n[4,5,6])
                if (cleaned.includes('\n[') && !cleaned.startsWith('[\n')) {
                    // Join all array lines into a single array
                    const lines = cleaned.split('\n').filter(line => line.trim());
                    const allNumbers = [];
                    lines.forEach(line => {
                        try {
                            const parsed = JSON.parse(line);
                            if (Array.isArray(parsed)) {
                                allNumbers.push(...parsed);
                            }
                        } catch (e) {
                            // Skip invalid lines
                        }
                    });
                    if (allNumbers.length > 0) {
                        cleaned = JSON.stringify(allNumbers);
                    }
                }
                
                // Remove trailing commas before closing brackets/braces
                cleaned = cleaned.replace(/,\s*([}\]])/g, '$1');
                
                // Log if there are issues
                if (!cleaned.startsWith('[') && !cleaned.startsWith('{')) {
                    console.error('Invalid JSON:', cleaned);
                }
                
                return cleaned;
            }

            const dates = JSON.parse(cleanJson(topics));
            
            // Handle topic categories - extract Topic field if it's an array of objects
            let topicList;
            try {
                const parsedTopics = JSON.parse(cleanJson(categories));
                if (Array.isArray(parsedTopics) && parsedTopics.length > 0 && typeof parsedTopics[0] === 'object' && parsedTopics[0].Topic) {
                    // Extract just the Topic field from objects
                    topicList = parsedTopics.map(item => item.Topic);
                } else {
                    topicList = parsedTopics;
                }
            } catch (e) {
                console.error('Error parsing topics:', e);
                topicList = ["Product Highlights", "Safety Tips", "Installation Guides"];
            }
            
            const platformList = JSON.parse(cleanJson(platforms));
            
            // Handle content types - extract only type names if it's an array of objects
            let typeList;
            try {
                const parsedTypes = JSON.parse(cleanJson(contentTypes));
                if (Array.isArray(parsedTypes) && parsedTypes.length > 0 && typeof parsedTypes[0] === 'object' && parsedTypes[0].Type) {
                    // Extract just the Type field from objects
                    typeList = parsedTypes.map(item => item.Type);
                } else {
                    typeList = parsedTypes;
                }
            } catch (e) {
                console.error('Error parsing content types:', e);
                typeList = ["Carousel Post", "Single Image", "Video Story", "Text Post", "Reel", "Poll"];
            }

            return { dates, topicList, platformList, typeList };
        }

        // Generate captions and hashtags for each post (parallel batch)
        async function generateContentDetails(posts) {
            updateStatus("Generating captions and hashtags in parallel...");

            const detailTasks = posts.map(post => {
                return callOllama(
                    `Write a social media caption for: ${post.topic}
Platform: ${post.platform}
Content type: ${post.contentType}
Length: 100-150 characters
Tone: Professional but engaging

Also generate 5 relevant hashtags.

Return ONLY JSON:
{
  "caption": "Your caption here...",
  "hashtags": ["#tag1", "#tag2", "#tag3", "#tag4", "#tag5"]
}`,
                    "You are a social media copywriter. Write engaging, platform-appropriate captions."
                ).then(result => {
                    try {
                        // Remove markdown code blocks
                        let cleaned = result.replace(/```json|```/g, "").trim();
                        // Remove trailing commas before closing brackets/braces
                        cleaned = cleaned.replace(/,\s*([}\]])/g, '$1');
                        const parsed = JSON.parse(cleaned);
                        return { 
                            ...post, 
                            caption: parsed.caption || 'No caption generated', 
                            hashtags: parsed.hashtags || [] 
                        };
                    } catch (e) {
                        console.error('Failed to parse caption JSON:', result);
                        return { 
                            ...post, 
                            caption: 'Error generating caption', 
                            hashtags: [] 
                        };
                    }
                });
            });

            // Process in batches of 5 to avoid overwhelming
            const batchSize = 5;
            const results = [];
            for (let i = 0; i < detailTasks.length; i += batchSize) {
                const batch = detailTasks.slice(i, i + batchSize);
                const batchResults = await Promise.all(batch);
                results.push(...batchResults);
                updateStatus(`Generated ${results.length}/${detailTasks.length} captions...`);
            }

            return results;
        }

        async function generateMonthlyPlan() {
            const btn = document.getElementById('generateBtn');
            const loader = document.getElementById('btnLoader');
            const statusBox = document.getElementById('statusBox');
            const statusText = document.getElementById('statusText');
            const progressBox = document.getElementById('progressBox');
            const progressOutput = document.getElementById('progressOutput');

            btn.disabled = true;
            loader.classList.remove('hidden');
            statusBox.classList.remove('hidden');
            progressBox.classList.remove('hidden');
            progressOutput.innerHTML = ''; // Clear previous progress

            try {
                // Check if Ollama is running
                updateStatus("Checking Ollama connection...");
                const isOllamaRunning = await checkOllamaConnection();
                
                if (!isOllamaRunning) {
                    throw new Error("Ollama is not running. Please start Ollama with: ollama serve");
                }

                updateStatus("Ollama connected. Starting planning...");

                const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                const monthName = monthNames[currentMonth];
                const daysInMonth = getDaysInMonth(currentMonth, currentYear);

                // Step 1: Plan all dates, topics, platforms, content types
                updateStatus("Planning monthly content structure...");
                const { dates, topicList, platformList, typeList } = await parallelAISearch(monthName, currentYear, daysInMonth);

                // Step 2: Generate and save each post individually
                updateStatus("Generating posts one by one...");
                monthlyPlan = {};
                
                // Filter dates based on platform selection
                const datesToGenerate = [];
                for (let i = 0; i < dates.length; i++) {
                    const platform = platformList[i % platformList.length];
                    if (currentPlatformFilter === 'all' || platform === currentPlatformFilter) {
                        datesToGenerate.push({ date: dates[i], index: i });
                    }
                }

                for (let j = 0; j < datesToGenerate.length; j++) {
                    const { date, index } = datesToGenerate[j];
                    const dateKey = `${currentYear}-${currentMonth + 1}-${date}`;
                    const topic = topicList[index % topicList.length];
                    const platform = platformList[index % platformList.length];
                    const contentType = typeList[index % typeList.length];
                    
                    updateStatus(`Generating post for ${monthName} ${date} (${j + 1}/${datesToGenerate.length})...`);
                    
                    // Generate caption and hashtags for this specific post
                    try {
                        // Check if it's a carousel post
                        const isCarousel = contentType.toLowerCase().includes('carousel');
                        
                        // Platform-specific image dimensions
                        const platformDimensions = {
                            'Instagram': { single: '1080x1080px (square)', story: '1080x1920px (9:16)', reel: '1080x1920px (9:16)', carousel: '1080x1080px (square per slide)' },
                            'Facebook': { single: '1200x630px (landscape)', story: '1080x1920px (9:16)', video: '1280x720px (16:9)', carousel: '1080x1080px (square per slide)' },
                            'Twitter': { single: '1600x900px (16:9)', video: '1280x720px (16:9)', poll: '1200x675px' },
                            'LinkedIn': { single: '1200x627px (landscape)', carousel: '1080x1080px (square per slide)', video: '1920x1080px (16:9)' }
                        };
                        const dims = platformDimensions[platform] || platformDimensions['Instagram'];
                        const imageSize = contentType.toLowerCase().includes('carousel') ? (dims.carousel || '1080x1080px') :
                                          contentType.toLowerCase().includes('story') ? (dims.story || '1080x1920px') :
                                          contentType.toLowerCase().includes('reel') ? (dims.reel || '1080x1920px') :
                                          contentType.toLowerCase().includes('video') ? (dims.video || '1280x720px') :
                                          (dims.single || '1080x1080px');

                        const captionResult = await callOllama(
                            `You are a world-class graphic designer with 15+ years of experience creating social media content for Fortune 500 companies. You specialize in creating visually stunning, brand-consistent designs.

Create a COMPLETE social media post package for:

Topic: ${topic}
Company: ${COMPANY_NAME} (${COMPANY_WEBSITE})
Industry: ${COMPANY_INDUSTRY}
Products/Services: ${COMPANY_KEY_PRODUCTS}
Target Audience: ${COMPANY_TARGET_AUDIENCE}
Brand Tone: ${COMPANY_TONE}
Brand Values: ${COMPANY_VALUES}

Platform: ${platform}
Content Type: ${contentType}
Image Dimensions: ${imageSize}

${isCarousel ? `IMPORTANT: This is a CAROUSEL POST. Generate 5-7 slide ideas/points for the carousel that showcase our products/services: ${COMPANY_KEY_PRODUCTS}` : ''}

Generate ALL of the following:

1. CAPTION: A compelling, ${COMPANY_TONE} caption (100-150 characters) optimized for ${platform}
2. HASHTAGS: 5 strategic hashtags aligned with brand values
3. IMAGE PROMPT (DALL-E/ChatGPT): A detailed, professional prompt to generate the complete post image. Include:
   - Exact visual composition and layout
   - Color palette (specific hex codes matching brand identity)
   - Typography style and text placement
   - Background design (gradients, patterns, textures)
   - Product/subject placement and styling
   - Lighting, mood, and atmosphere
   - Brand logo placement suggestion
   - Image dimensions: ${imageSize}
   - Photo-realistic or illustration style as appropriate
   - All text overlays that should appear on the image
4. MIDJOURNEY PROMPT: A Midjourney-optimized version with parameters (--ar, --v, --style, --q)
${isCarousel ? `5. CAROUSEL SLIDES: 5-7 slide descriptions with individual image prompts for each slide` : ''}

Return ONLY valid JSON:
{
  "caption": "Your compelling caption...",
  "hashtags": ["#tag1", "#tag2", "#tag3", "#tag4", "#tag5"],
  "imagePrompt": {
    "dalle": "Detailed DALL-E/ChatGPT image generation prompt with complete visual description including layout, colors, typography, elements, dimensions ${imageSize}...",
    "midjourney": "Midjourney prompt with --ar 1:1 --v 6 --style raw --q 2 parameters...",
    "designNotes": "Brief design rationale: color psychology, layout principles, brand alignment notes"
  }${isCarousel ? `,
  "carouselSlides": [
    {
      "title": "Slide 1 title",
      "description": "What this slide covers",
      "slideImagePrompt": "Complete image prompt for this specific slide..."
    }
  ]` : ''}
}`,
                            `You are an elite graphic designer and social media strategist for ${COMPANY_NAME} with 15+ years of experience at top agencies. You create designs that are visually stunning, on-brand, and drive engagement. Your image prompts are detailed enough to produce print-ready, professional social media posts when used with DALL-E, Midjourney, or ChatGPT image generation. Always include specific colors, typography, layout, dimensions, and visual elements.`
                        );
                        
                        let cleaned = captionResult.replace(/```json|```/g, "").trim();
                        cleaned = cleaned.replace(/,\s*([}\]])/g, '$1');
                        const parsed = JSON.parse(cleaned);
                        
                        monthlyPlan[dateKey] = {
                            topic: topic,
                            platform: platform,
                            contentType: contentType,
                            caption: parsed.caption || 'No caption generated',
                            hashtags: parsed.hashtags || [],
                            imagePrompt: parsed.imagePrompt || null,
                            ...(parsed.carouselSlides && { carouselSlides: parsed.carouselSlides }),
                            postTime: "10:00",
                            status: "pending"
                        };
                        
                        // Save immediately after each post generation
                        await savePlan();
                        renderCalendar();
                        
                        updateStatus(`Generated post for ${monthName} ${date} (${j + 1}/${datesToGenerate.length})`);
                        
                    } catch (error) {
                        console.error(`Error generating post for ${dateKey}:`, error);
                        monthlyPlan[dateKey] = {
                            topic: topic,
                            platform: platform,
                            contentType: contentType,
                            caption: 'Error generating caption',
                            hashtags: [],
                            imagePrompt: null,
                            postTime: "10:00",
                            status: "pending"
                        };
                        await savePlan();
                        renderCalendar();
                    }
                }

                statusText.textContent = 'Monthly plan generated successfully!';
                setTimeout(() => {
                    statusBox.classList.add('hidden');
                    progressBox.classList.add('hidden');
                }, 3000);

            } catch (error) {
                console.error(error);
                statusText.textContent = `Error: ${error.message}. Ensure Ollama is running with gemma4:latest model.`;
                statusBox.classList.add('bg-red-50', 'border-red-600');
                setTimeout(() => {
                    statusBox.classList.remove('bg-red-50', 'border-red-600');
                    statusBox.classList.add('hidden');
                    progressBox.classList.add('hidden');
                }, 5000);
            } finally {
                btn.disabled = false;
                loader.classList.add('hidden');
            }
        }

        function updateStatus(text) {
            document.getElementById('statusText').textContent = text;
        }

        async function savePlan() {
            const key = `socialPlan_${currentYear}_${currentMonth}`;
            try {
                const response = await fetch('storage.php?action=save', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ key, plan: monthlyPlan })
                });
                const result = await response.json();
                if (!result.success) {
                    console.error('Failed to save plan:', result);
                }
            } catch (error) {
                console.error('Error saving plan:', error);
            }
        }

        async function loadSavedPlan() {
            const key = `socialPlan_${currentYear}_${currentMonth}`;
            try {
                const response = await fetch(`storage.php?action=load&key=${key}`);
                if (!response.ok) {
                    console.error('Failed to load plan:', response.status);
                    return;
                }
                const text = await response.text();
                if (!text || text.trim() === '') {
                    console.log('No saved plan found');
                    return;
                }
                const saved = JSON.parse(text);
                if (saved && Object.keys(saved).length > 0) {
                    monthlyPlan = saved;
                    renderCalendar();
                }
            } catch (error) {
                console.error('Error loading plan:', error);
            }
        }

        function updateStats() {
            // Filter posts by current platform
            const filteredPosts = Object.values(monthlyPlan).filter(p => 
                currentPlatformFilter === 'all' || p.platform === currentPlatformFilter
            );
            
            const total = filteredPosts.length;
            const scheduled = filteredPosts.filter(p => p.status === 'scheduled').length;
            const pending = total - scheduled;
            const completion = total > 0 ? Math.round((scheduled / total) * 100) : 0;

            document.getElementById('totalPosts').textContent = total;
            document.getElementById('scheduledPosts').textContent = scheduled;
            document.getElementById('pendingPosts').textContent = pending;
            document.getElementById('completionRate').textContent = completion + '%';
        }

        function openEditorialModal(dateKey) {
            const content = monthlyPlan[dateKey];
            const modal = document.getElementById('editorialModal');
            const title = document.getElementById('modalTitle');
            const modalContent = document.getElementById('modalContent');

            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            const [year, month, day] = dateKey.split('-');

            title.textContent = `${monthNames[month - 1]} ${day}, ${year}`;

            modalContent.innerHTML = `
                <div class="space-y-4">
                    <div class="bg-blue-50 p-4 rounded-xl">
                        <label class="block text-sm font-semibold text-blue-800 mb-1">Topic</label>
                        <input type="text" id="editTopic" value="${content.topic}" class="w-full p-2 border border-blue-200 rounded-lg" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-4 rounded-xl">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Platform</label>
                            <select id="editPlatform" class="w-full p-2 border border-gray-200 rounded-lg">
                                <option ${content.platform === 'Instagram' ? 'selected' : ''}>Instagram</option>
                                <option ${content.platform === 'LinkedIn' ? 'selected' : ''}>LinkedIn</option>
                                <option ${content.platform === 'Twitter' ? 'selected' : ''}>Twitter</option>
                                <option ${content.platform === 'Facebook' ? 'selected' : ''}>Facebook</option>
                            </select>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-xl">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Content Type</label>
                            <input type="text" id="editContentType" value="${content.contentType}" class="w-full p-2 border border-gray-200 rounded-lg" />
                        </div>
                    </div>

                    <div class="space-y-4">
                    <div class="bg-gray-50 p-4 rounded-xl">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Caption</label>
                        <textarea id="editCaption" rows="3" class="w-full p-2 border border-gray-200 rounded-lg">${content.caption || ''}</textarea>
                    </div>
                    
                    ${content.carouselSlides ? `
                    <div class="bg-blue-50 p-4 rounded-xl border-2 border-blue-200">
                        <label class="block text-sm font-semibold text-blue-700 mb-2">Carousel Slides (${content.carouselSlides.length} slides)</label>
                        <div class="space-y-2">
                            ${content.carouselSlides.map((slide, idx) => {
                                let slideContent = '';
                                let slidePrompt = '';
                                if (typeof slide === 'string') {
                                    slideContent = slide;
                                } else if (typeof slide === 'object') {
                                    if (slide.title) slideContent = slide.title;
                                    else if (slide.caption) slideContent = slide.caption;
                                    else if (slide.description) slideContent = slide.description;
                                    else if (slide.content) slideContent = slide.content;
                                    else slideContent = JSON.stringify(slide, null, 2);
                                    if (slide.slideImagePrompt) slidePrompt = slide.slideImagePrompt;
                                    if (slide.description && slide.title) slideContent = slide.title + ': ' + slide.description;
                                }
                                return `
                                <div class="bg-white p-3 rounded-lg border border-blue-100">
                                    <span class="text-xs font-bold text-blue-600">Slide ${idx + 1}</span>
                                    <p class="text-sm text-gray-700 mt-1">${slideContent}</p>
                                    ${slidePrompt ? `
                                    <div class="mt-2 prompt-box">
                                        <p class="text-xs text-purple-600 font-semibold mb-1"><i class="fas fa-image mr-1"></i>Slide Image Prompt:</p>
                                        <p class="text-xs text-gray-600 bg-purple-50 p-2 rounded border border-purple-100 pr-16">${slidePrompt}</p>
                                        <button onclick="copyToClipboard(this, '${slidePrompt.replace(/'/g, "\\'").replace(/"/g, '&quot;')}')" class="copy-btn px-2 py-1 text-xs bg-purple-100 hover:bg-purple-200 text-purple-700 rounded transition-all">
                                            <i class="fas fa-copy"></i> Copy
                                        </button>
                                    </div>
                                    ` : ''}
                                </div>
                            `}).join('')}
                        </div>
                    </div>
                    ` : ''}

                    ${content.imagePrompt ? `
                    <div class="bg-purple-50 p-4 rounded-xl border-2 border-purple-200">
                        <div class="flex items-center justify-between mb-3">
                            <label class="block text-sm font-bold text-purple-800">
                                <i class="fas fa-paint-brush mr-2"></i>AI Image Generation Prompts
                            </label>
                            <span class="text-xs text-purple-500 bg-purple-100 px-2 py-1 rounded-full">Ready to use with DALL-E, Midjourney, ChatGPT</span>
                        </div>

                        <div class="prompt-tabs flex gap-2 mb-3">
                            <button class="prompt-tab active px-3 py-1.5 rounded-lg text-xs font-semibold" onclick="switchPromptTab(this, 'dalle-prompt-${dateKey.replace(/-/g, '')}')">
                                <i class="fas fa-robot mr-1"></i>DALL-E / ChatGPT
                            </button>
                            <button class="prompt-tab px-3 py-1.5 rounded-lg text-xs font-semibold" onclick="switchPromptTab(this, 'mj-prompt-${dateKey.replace(/-/g, '')}')">
                                <i class="fas fa-magic mr-1"></i>Midjourney
                            </button>
                            ${content.imagePrompt.designNotes ? `
                            <button class="prompt-tab px-3 py-1.5 rounded-lg text-xs font-semibold" onclick="switchPromptTab(this, 'notes-prompt-${dateKey.replace(/-/g, '')}')">
                                <i class="fas fa-lightbulb mr-1"></i>Design Notes
                            </button>
                            ` : ''}
                        </div>

                        <div id="dalle-prompt-${dateKey.replace(/-/g, '')}" class="prompt-content prompt-box">
                            <div class="bg-white p-4 rounded-lg border border-purple-100 pr-20">
                                <p class="text-xs font-semibold text-purple-700 mb-2">DALL-E / ChatGPT Image Prompt:</p>
                                <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">${typeof content.imagePrompt === 'string' ? content.imagePrompt : (content.imagePrompt.dalle || 'No DALL-E prompt generated')}</p>
                            </div>
                            <button onclick="copyPromptToClipboard(this, '${dateKey}', 'dalle')" class="copy-btn px-3 py-1.5 text-xs bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-all shadow-sm">
                                <i class="fas fa-copy mr-1"></i>Copy Prompt
                            </button>
                        </div>

                        <div id="mj-prompt-${dateKey.replace(/-/g, '')}" class="prompt-content prompt-box hidden">
                            <div class="bg-white p-4 rounded-lg border border-purple-100 pr-20">
                                <p class="text-xs font-semibold text-purple-700 mb-2">Midjourney Prompt:</p>
                                <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">${content.imagePrompt.midjourney || 'No Midjourney prompt generated'}</p>
                            </div>
                            <button onclick="copyPromptToClipboard(this, '${dateKey}', 'midjourney')" class="copy-btn px-3 py-1.5 text-xs bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-all shadow-sm">
                                <i class="fas fa-copy mr-1"></i>Copy Prompt
                            </button>
                        </div>

                        ${content.imagePrompt.designNotes ? `
                        <div id="notes-prompt-${dateKey.replace(/-/g, '')}" class="prompt-content prompt-box hidden">
                            <div class="bg-white p-4 rounded-lg border border-purple-100">
                                <p class="text-xs font-semibold text-purple-700 mb-2">Design Rationale:</p>
                                <p class="text-sm text-gray-700 leading-relaxed">${content.imagePrompt.designNotes}</p>
                            </div>
                        </div>
                        ` : ''}
                    </div>
                    ` : ''}
                    
                    <div class="bg-gray-50 p-4 rounded-xl">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Hashtags</label>
                        <input type="text" id="editHashtags" value="${content.hashtags ? content.hashtags.join(' ') : ''}" class="w-full p-2 border border-gray-200 rounded-lg" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-4 rounded-xl">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Post Time</label>
                            <input type="time" id="editPostTime" value="${content.postTime || '10:00'}" class="w-full p-2 border border-gray-200 rounded-lg" />
                        </div>
                        <div class="bg-gray-50 p-4 rounded-xl">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                            <select id="editStatus" class="w-full p-2 border border-gray-200 rounded-lg">
                                <option ${content.status === 'pending' ? 'selected' : ''}>Pending</option>
                                <option ${content.status === 'scheduled' ? 'selected' : ''}>Scheduled</option>
                                <option ${content.status === 'posted' ? 'selected' : ''}>Posted</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button onclick="saveEditorialChanges('${dateKey}')" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition-all">
                            <i class="fas fa-save mr-2"></i>Save Changes
                        </button>
                        <button onclick="deletePost('${dateKey}')" class="px-6 py-3 bg-red-100 hover:bg-red-200 text-red-700 font-semibold rounded-xl transition-all">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function switchPromptTab(btn, targetId) {
            const parent = btn.closest('.bg-purple-50');
            parent.querySelectorAll('.prompt-tab').forEach(t => t.classList.remove('active'));
            btn.classList.add('active');
            parent.querySelectorAll('.prompt-content').forEach(c => c.classList.add('hidden'));
            document.getElementById(targetId).classList.remove('hidden');
        }

        function copyToClipboard(btn, text) {
            navigator.clipboard.writeText(text).then(() => {
                btn.classList.add('copied');
                btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
                setTimeout(() => {
                    btn.classList.remove('copied');
                    btn.innerHTML = '<i class="fas fa-copy"></i> Copy';
                }, 2000);
            });
        }

        function copyPromptToClipboard(btn, dateKey, type) {
            const content = monthlyPlan[dateKey];
            let text = '';
            if (content && content.imagePrompt) {
                if (typeof content.imagePrompt === 'string') {
                    text = content.imagePrompt;
                } else {
                    text = content.imagePrompt[type] || content.imagePrompt.dalle || '';
                }
            }
            navigator.clipboard.writeText(text).then(() => {
                btn.classList.add('copied');
                btn.innerHTML = '<i class="fas fa-check mr-1"></i>Copied!';
                setTimeout(() => {
                    btn.classList.remove('copied');
                    btn.innerHTML = '<i class="fas fa-copy mr-1"></i>Copy Prompt';
                }, 2000);
            });
        }

        function closeModal() {
            document.getElementById('editorialModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function saveEditorialChanges(dateKey) {
            const existingData = monthlyPlan[dateKey] || {};
            monthlyPlan[dateKey] = {
                topic: document.getElementById('editTopic').value,
                platform: document.getElementById('editPlatform').value,
                contentType: document.getElementById('editContentType').value,
                caption: document.getElementById('editCaption').value,
                hashtags: document.getElementById('editHashtags').value.split(',').map(h => h.trim()).filter(h => h),
                imagePrompt: existingData.imagePrompt || null,
                ...(existingData.carouselSlides && { carouselSlides: existingData.carouselSlides }),
                postTime: document.getElementById('editPostTime').value,
                status: document.getElementById('editStatus').value
            };

            savePlan();
            renderCalendar();
            closeModal();
        }

        function deletePost(dateKey) {
            if (confirm('Are you sure you want to delete this post?')) {
                delete monthlyPlan[dateKey];
                savePlan();
                renderCalendar();
                closeModal();
            }
        }

        // Close modal on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeModal();
        });

        // Close modal on outside click
        document.getElementById('editorialModal').addEventListener('click', (e) => {
            if (e.target.id === 'editorialModal') closeModal();
        });
    </script>
</body>
</html>
