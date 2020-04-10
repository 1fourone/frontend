/* 
    vbe.js
    ----
    Responsible for handling all visual block element information
*/

const difficulties = ["easy", "medium", "hard"];

function createQuestionVBE(question, index, isDraggable=true) {
    var wrapper = document.createElement("div");
    wrapper.setAttribute("class", "question");
    wrapper.setAttribute("id", "q-" + index);
    wrapper.setAttribute("draggable", isDraggable);

    var table = document.createElement("table");

    /* Populate table with question object data */
    var firstRow = document.createElement("tr");

    /* First row has creator and difficulty */
    var creator = document.createElement("td");
    creator.innerHTML = "👤 " + question["creatorName"];
    firstRow.appendChild(creator);
    var difficulty = document.createElement("td");
    difficulty.innerHTML = "🧠 " + difficulties[question["difficulty"]].padEnd(6);
    firstRow.appendChild(difficulty);

    table.appendChild(firstRow);

    /* Second row has topic and constraint */
    var secondRow = document.createElement("tr");

    var creator = document.createElement("td");
    creator.innerHTML = "📚 " + question["topic"];
    secondRow.appendChild(creator);
    var difficulty = document.createElement("td");
    difficulty.innerHTML = "🔒 " + question["constraint"];
    secondRow.appendChild(difficulty);

    table.appendChild(secondRow);

    /* Third row has the prompt */
    var thirdRow = document.createElement("tr");
    thirdRow.setAttribute("class", "question-prompt");

    var prompt = document.createElement("td");
    prompt.innerHTML = "🛈 " + question["prompt"];
    thirdRow.appendChild(prompt);

    table.appendChild(thirdRow);

    wrapper.appendChild(table);
    return wrapper;
}

function createQuestionListVBE(questions=[], isDraggable=true) {
    var wrapper = document.createElement("div");
    wrapper.setAttribute("class", "question-list");

    for(let i=0; i < questions.length; i++) {
        wrapper.appendChild(createQuestionVBE(questions[i], i, isDraggable));
    }

    var referenceNode = document.getElementById("placeholder");
    referenceNode.parentNode.insertBefore(wrapper, referenceNode.nextSibling);
}

function test() {
    const questions = [
        {
        "id": "1231j2k3j1l2k3j",
        "prompt": "Write a function named add that takes parameters a b, adds them together and prints the result",
        "difficulty": 0,
        "topic": "Arithmetic",
        "constraint": "print",
        "creatorName": "ma123"
        },
        {
            "id": "1231j2k3j152k3j",
            "prompt": "Write a function named sub that takes parameters a b, subtracts them and prints the result",
            "difficulty": 1,
            "topic": "Arithmetic",
            "constraint": "print",
            "creatorName": "ma123"
        },
        {
            "id": "1231j2k3j1l2k3j",
            "prompt": "Write a function named stretchTuple that takes parameter t, multiplies every element in t by 3 and returns the result",
            "difficulty": 1,
            "topic": "Tuples",
            "constraint": "for",
            "creatorName": "ma123"
        },
        {
            "id": "1231j2k3j1l2k3j",
            "prompt": "Write a function named stretchArr that takes parameter a, multiplies every element in a by 4 and returns the result",
            "difficulty": 1,
            "topic": "Arrays",
            "constraint": "for",
            "creatorName": "ma123"
        },
        {
            "id": "1231j2k3j1l2k3j",
            "prompt": "Write a function named findTurtle that takes parameter wordList, finds the index first occurrence of 'sheep' and returns it",
            "difficulty": 2,
            "topic": "Arrays",
            "constraint": "while",
            "creatorName": "ma123"
        }
    ]

    createQuestionListVBE(questions, true);
}