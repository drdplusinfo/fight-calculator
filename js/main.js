window.addEventListener('load', function () {
    let withSkillRanksNodes = document.getElementsByClassName('with-skill-ranks');
    for (let nodeIndex = 0, nodesLength = withSkillRanksNodes.length; nodeIndex < nodesLength; nodeIndex++) {
        let withSkillRanksNode = withSkillRanksNodes[nodeIndex];
        let select = withSkillRanksNode.getElementsByTagName('select')[0];
        let skillRanksRaw = withSkillRanksNode.getElementsByClassName('skill-ranks')[0];
        let previousSkillRanks = JSON.parse(skillRanksRaw.dataset.previousSkillRanks);
        select.addEventListener('change', function (event) {
            let selectedValue = event.target.value;
            let previousSkillRankValue = null;
            if (previousSkillRanks.hasOwnProperty(selectedValue)) {
                previousSkillRankValue = previousSkillRanks[selectedValue].toString();
            }
            if (previousSkillRankValue === null) {
                previousSkillRankValue = '0';
            }
            let skillRankInputs = withSkillRanksNode.getElementsByTagName('input');
            for (let rankIndex = 0, ranksLength = skillRankInputs.length; rankIndex < ranksLength; rankIndex++) {
                let skillRankInput = skillRankInputs[rankIndex];
                if (skillRankInput.value === previousSkillRankValue) {
                    skillRankInput.checked = true;
                    return;
                }
            }
        });
    }
});