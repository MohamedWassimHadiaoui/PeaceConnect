<?php
if (!defined('XAI_API_KEY')) {
    define('XAI_API_KEY', 'xai-VjrvKAE8bO3FxMznEO5N9uUdcnBqSmt5QOGcXx27ie4LN903pv23PwgxtOXt61uKaiZCu2FnkxvORHfx');
}

if (!defined('XAI_API_URL')) {
    define('XAI_API_URL', 'https://api.x.ai/v1/chat/completions');
}

if (!defined('XAI_MODEL')) {
    define('XAI_MODEL', 'grok-4-fast-non-reasoning');
}

if (!defined('VIOLENCE_KEYWORDS')) {
    define('VIOLENCE_KEYWORDS', [
        'violence', 'attack', 'assault', 'hit', 'beat', 'kill', 'murder', 
        'knife', 'gun', 'weapon', 'threat', 'threaten', 'harm', 'hurt',
        'fight', 'punch', 'stab', 'shoot', 'abuse', 'brutal'
    ]);
}

if (!defined('URGENCY_KEYWORDS')) {
    define('URGENCY_KEYWORDS', [
        'urgent', 'urgency', 'asap', 'immediately', 'right now', 'emergency', 
        'danger', 'help me', 'police', 'ambulance', 'hospital', 'critical',
        'life threatening', 'dying', 'scared', 'afraid', 'now'
    ]);
}

if (!defined('HARASSMENT_KEYWORDS')) {
    define('HARASSMENT_KEYWORDS', [
        'harassment', 'harass', 'bully', 'bullying', 'intimidation', 
        'stalk', 'stalking', 'threats', 'cyber', 'online abuse',
        'trolling', 'doxxing', 'blackmail'
    ]);
}

if (!defined('DISCRIMINATION_KEYWORDS')) {
    define('DISCRIMINATION_KEYWORDS', [
        'discrimination', 'racism', 'racist', 'sexism', 'sexist', 
        'homophobia', 'xenophobia', 'hate', 'slur', 'prejudice',
        'bigot', 'intolerance'
    ]);
}
