=== AI Bot for bbPress ===
Contributors: chubes
Tags: bbpress, ai, bot, forum, chatgpt, anthropic, claude, gemini, grok, openrouter
Requires at least: 6.9
Tested up to: 6.9
Stable tag: 2.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: ai-bot-for-bbpress

AI bot for bbPress that uses WordPress core AI Credentials + Abilities for agentic search.

== Description ==

AI Bot for bbPress integrates seamlessly with your bbPress forums, allowing a configurable AI bot user to participate in discussions.

The bot can be triggered by direct mentions (@YourBotUsername) or specific keywords within forum posts. When needed, it can search your local forum content to retrieve relevant context before answering.

**Key Features:**
* **AI Credentials via WordPress Core** - Configure provider credentials in Settings > AI Credentials
* **Agentic Search** - The model decides when to call search abilities
* **Bot Triggers** - Responds to @mentions and configurable keywords
* **Forum Access Control** - Limit bot participation to selected forums


**Links:**
* [Plugin Homepage](https://chubes.net) - Visit the developer's website
* [GitHub Repository](https://github.com/chubes4/ai-bot-for-bbpress) - Source code, issues, and contributions
* [Support](https://github.com/chubes4/ai-bot-for-bbpress/issues) - Report bugs or request features

== Installation ==

1.  Upload the `ai-bot-for-bbpress` folder to the `/wp-content/plugins/` directory
2.  Activate the plugin through the 'Plugins' menu in WordPress
3.  Configure the plugin settings under the 'AI Bot for bbPress' menu in the WordPress admin panel (Settings > Forum AI Bot).

== Configuration ==

1. Configure AI provider credentials in WordPress core:

* Go to Settings > AI Credentials
* Add credentials for your preferred provider

2. Configure the bot settings:

After activation, navigate to Settings > Forum AI Bot in your WordPress admin area to configure the following options:

* **Bot User ID:** The WordPress user ID of the account the bot will use to post replies.
* **System Prompt:** Instructions defining the bot's personality, role, and general behavior.
* **Custom Prompt:** Additional instructions appended to every request.
* **Temperature:** Controls creativity/randomness (0 = deterministic, 1 = max creativity). Default is 0.5.
* **Trigger Keywords:** A comma-separated list of keywords (in addition to mentions) that will trigger the bot to respond.
* **Enable Local Search:** Allow the bot to use the built-in local search ability.
* **Local Search Limit:** Maximum number of relevant posts/topics to retrieve from the local forum database for context.
* **Forum Access Control:** Choose whether the bot responds in all forums or only selected ones.

== Frequently Asked Questions ==

= What does this plugin do? =

This plugin allows you to integrate an AI bot into your bbPress forums. The bot can respond to mentions and keywords, providing automated assistance and engaging in discussions.

= How do I configure the bot? =

You can configure the bot's behavior and settings under the 'AI Bot for bbPress' menu in the WordPress admin panel.


== External Services ==

This plugin connects to your selected AI provider's API to generate responses for the AI bot. This is essential for the plugin's core functionality of providing AI-driven replies in bbPress forums.

*   **Supported Services:** OpenAI API, Anthropic API, Google Gemini API, Grok (X.AI) API, or OpenRouter API - depending on your configuration.
*   **Purpose:** To generate intelligent and contextually relevant responses based on forum discussions and configured prompts.
*   **Data Sent:** When the bot is triggered (by a mention or keyword), the following types of data are sent to your selected AI provider's API:
    *   The content of the post that triggered the bot.
    *   Relevant conversation history from the current topic (including post content and author usernames/slugs).
    *   Contextual information retrieved from the local WordPress database (titles, snippets, and URLs of relevant posts/pages based on keyword matching).
    *   The system prompt, custom prompt, and temperature settings configured in the plugin's admin page.
    *   The structure of your bbPress forums (forum names, topic names, and their hierarchy).
    *   The current date and time.
*   **When Data is Sent:** Data is sent only when the bot is triggered to generate a response. This occurs after a user posts a new reply or topic that meets the trigger conditions (mentioning the bot or containing a specified keyword).
*   **Privacy Policies:** Please review the privacy policy and terms of service for your selected AI provider to understand how they handle the data sent to their API.

It is important to have an active API key with sufficient credits/quota for your selected AI provider for the bot to function. Each provider has different pricing models and terms.

== Screenshots ==

1.  Configuration screen showing the main settings.
2.  Example of a bot reply in a forum topic.
3.  (Add more descriptions as needed)

== Upgrade Notice ==

= 1.0.0 =
This is the version submitted to the WordPress plugin repository. 

= 0.1.2 =
*   Updated plugin name and description.
*   Added helper plugin for remote context.

= 0.1.1 =
*   Bug fixes and minor improvements.

= 0.1.0 =
*   Initial release.