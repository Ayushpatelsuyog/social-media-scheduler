# Social Media Scheduler

AI-Powered Monthly Content Planning Tool for Social Media.

## Features

- Monthly calendar view for social media content planning
- AI-powered content generation using Ollama (local LLM)
- Multi-platform support (Instagram, Facebook, Twitter, LinkedIn)
- Generates captions, hashtags, and AI image prompts for each post
- Platform-specific content type recommendations
- Carousel slide planning
- Edit and manage individual posts
- Persistent storage for generated plans

## Requirements

- PHP 7.4+ with curl extension
- [Ollama](https://ollama.ai/) running locally
- A supported Ollama model (default: `gemma3:latest`)

## Setup

1. Clone this repository
2. Install and start Ollama: `ollama serve`
3. Pull the model: `ollama pull gemma3:latest`
4. Configure your company details in `config.php`
5. Start a PHP development server:
   ```bash
   php -S localhost:8000
   ```
6. Open `http://localhost:8000` in your browser

## Configuration

Edit `config.php` to customize:
- `OLLAMA_API_URL` - Ollama API endpoint
- `OLLAMA_MODEL` - AI model to use
- Company details (name, industry, products, audience, tone, etc.)

## Usage

1. Select the month and year
2. Optionally filter by platform
3. Click "Generate Monthly Plan" to create AI-powered content
4. Click on any day to view/edit the post details
5. Copy the AI image prompt to use with DALL-E, Midjourney, or ChatGPT

## File Structure

```
├── index.php        # Main application (calendar UI + JavaScript)
├── config.php       # Configuration constants
├── navbar.php       # Navigation bar component
├── chat.php         # Ollama API streaming proxy
├── storage.php      # Server-side plan storage
├── data/            # Stored plans (auto-created, git-ignored)
└── README.md
```
