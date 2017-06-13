window.addEventListener('load', function () {
        // on change of skill change its related skill rank also
        let withSkillRanksNodes = document.getElementsByClassName('with-skill-ranks');
        for (let nodeIndex = 0, nodesLength = withSkillRanksNodes.length; nodeIndex < nodesLength; nodeIndex++) {
            let withSkillRanksNode = withSkillRanksNodes[nodeIndex];
            let select = withSkillRanksNode.getElementsByTagName('select')[0];
            let historySkillRanksNode = withSkillRanksNode.getElementsByClassName('skill-ranks')[0];
            let historySkillRanks = JSON.parse(historySkillRanksNode.dataset.historySkillRanks);
            let skillRankInputs = withSkillRanksNode.getElementsByTagName('input');
            select.addEventListener('change', function (event) {
                let selectedValue = event.target.value;
                let historySkillRankValue = null;
                if (historySkillRanks.hasOwnProperty(selectedValue)) {
                    historySkillRankValue = historySkillRanks[selectedValue].toString();
                }
                if (historySkillRankValue === null) {
                    historySkillRankValue = '0';
                }
                for (let rankIndex = 0, ranksLength = skillRankInputs.length; rankIndex < ranksLength; rankIndex++) {
                    let skillRankInput = skillRankInputs[rankIndex];
                    if (skillRankInput.value === historySkillRankValue) {
                        skillRankInput.checked = true;
                        return;
                    }
                }
            });
            // on direct change of skill rank remember that value for current skill for current session
            for (let rankIndex = 0, ranksLength = skillRankInputs.length; rankIndex < ranksLength; rankIndex++) {
                let skillRankInput = skillRankInputs[rankIndex];
                skillRankInput.addEventListener('change', function (event) {
                    if (!event.target.checked) {
                        return;
                    }
                    historySkillRanks[select.value] = event.target.value;
                });
            }
        }
    }
);

window.addEventListener('load', function () {
    // scroll to previous position on page reload
    let scrollFromTopNode = document.getElementById('scrollFromTop');
    let body = document.getElementsByTagName('body')[0];
    if (scrollFromTopNode.value > 0) {
        body.scrollTop = scrollFromTopNode.value;
    }
    // remember scroll position on submit to restore it after page reload
    let forms = document.getElementsByTagName('form');
    for (let formIndex = 0, formsLength = forms.length; formIndex < formsLength; formIndex++) {
        let form = forms[formIndex];
        form.addEventListener('submit', function () {
            scrollFromTopNode.value = body.scrollTop;
        });
    }
});