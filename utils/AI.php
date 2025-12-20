<?php
require_once __DIR__ . '/../config/ai.php';

function generateLessonPlan($topic) {
    return generateText("
    Your response should be **HTML-formatted**. Please **do not use markdown formatting** or code block markers. Use HTML tags like <h2>, <h3>, <ul>, <li>, <p>, <strong>, and <em> to structure your answers clearly.

    Create a structured lesson plan for the topic '$topic', **designed for a chatbot or conversational interface**, prioritizing **hands-on learning, tutorials, and interactive exercises**. The lesson should be suitable for step-by-step delivery, allowing the chatbot to guide learners through explanations, examples, and practical tasks.

    MUST Include the following sections:

    1. Introduction: Brief overview of the topic, key objectives, and what learners will achieve by the end. Include an invitation for the learner to participate in exercises.
    2. Section 1: Introduce the first key concept. Provide a short explanation, a step-by-step tutorial or example, and an interactive exercise the chatbot can present to the learner. Include hints or prompts the bot can use.
    3. Section 2: Present the second concept or technique. Include instructions, examples, and another interactive exercise or mini-project suitable for conversational delivery.
    4. Section 3: Cover additional concepts, advanced techniques, or alternative perspectives. Include exercises or reflective tasks that the chatbot can guide the learner through step-by-step.
    5. Conclusion: Summarize key points and learning outcomes. Include follow-up exercises, challenges, or resources the chatbot can suggest for continued practice.

    The goal is to create a **practical, step-by-step tutorial experience** in a conversational format, with interactive exercises and guidance suitable for chatbot delivery.

    Again the topic is: $topic
    ");
}

function generateQuiz($plan) {
    $prompt = <<<PROMPT
    Create a quiz in **valid JSON format only**. Output must be **exactly one line**, no spaces, no newlines, no indentation, no extra characters. No markdown, no explanations, no commentary.

    Requirements:
    - Exactly 10 questions.
    - Each question must have:
    - "question": string
    - "choices": array of exactly 4 strings
    - "answer": string, must exactly match one of the choices
    - No additional fields, no notes, nothing outside the JSON.

    Output format must be exactly:

    {"quiz":[{"question":"string","choices":["choice1","choice2","choice3","choice4"],"answer":"choice1"}]}

    Use this lesson plan as the basis for all questions:

    $plan
    PROMPT;
    return generateText($prompt);
}

function generateChatResponse($plan, $section, $lastoutput, $userinput, $studentName) {
    return generateText(
    "You are an AI tutor. Your responses must be direct, factual, and instructional.

    CRITICAL LANGUAGE RULE:
    - Do NOT use conversational openers, transitions, or motivational phrases.
    - Do NOT say phrases like:
    \"Let's...\"
    \"Let's dive into...\"
    \"Great question...\"
    \"Here's how...\"
    \"We'll explore...\"
    - The first sentence MUST begin with a factual statement about the topic itself.

    Inputs (internal use only — never repeat or describe them):
    - Lesson Plan: {{$plan}}
    - Current Section: {{$section}}
    - Previous AI Output: {{$lastoutput}}
    - Latest Student Input: {{$userinput}}
    - Student Name: {{$studentName}}

    Teaching rules (non-negotiable):
    1. Teach ONLY the CURRENT SECTION ({{$section}}).
    2. Never advance sections unless {{$section}} changes.
    3. Decline any request to skip ahead and continue teaching the current section.
    4. If off-topic, redirect briefly and continue teaching.
    5. Simplify and give examples if confusion is likely.

    Tone rules:
    - Neutral, clear, professional.
    - No enthusiasm language.
    - No filler.
    - Short sentences preferred.
    - Plain instructional wording.

    Formatting rules (STRICT):
    - HTML only
    - No markdown
    - No '*' character
    - No <body> tag
    - No section-title headers that restate {{$section}}

    Response structure (MANDATORY):
    1. Begin immediately with factual content (no acknowledgement).
    2. Provide explanation, steps, or examples for the current section.
    3. Optionally include a brief practice or clarification.
    4. End with 3-5 short follow-up questions using <ol>.
    5. Explicitly tell the student they can reply with just the number.

    FIRST SENTENCE EXAMPLES (GOOD):
    \"Body planes are imaginary flat surfaces used to describe locations and movements of structures in the body.\"
    \"Interactive exercises improve retention by requiring active recall and application.\"

    FIRST SENTENCE EXAMPLES (BAD):
    \"Let's dive into body planes...\"
    \"Great question about...\"
    \"You're interested in...\"

    Failure to follow these rules makes the response invalid."
    );
}

function generateText($input) {
    $model = 'meta-llama/llama-4-scout-17b-16e-instruct';
    $url = 'https://api.groq.com/openai/v1/chat/completions';

    $data = [
        "model" => $model,
        "messages" => [
            [
                "role" => "user",
                "content" => $input
            ]
        ],
        "max_tokens" => 1024,
        "temperature" => 0,
        "top_p" => 0.9,
        "stream" => false
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer " . aiAPIKey
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo "Curl error: " . curl_error($ch);
    }

    $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    $decoded = json_decode($response, true);

    if ($http_status >= 400 || isset($decoded['error']) || !isset($decoded['choices'][0]['message']['content'])) {
        throw new Exception('Groq API error or unexpected response');
    }

    return $decoded['choices'][0]['message']['content'];
}

?>