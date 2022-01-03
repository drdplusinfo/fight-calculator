(() => {
    const hash = window.location.hash
    if (!hash) {
        return
    }

    const postMatches = hash.match(/^#[!]clanky[/](.+)$/)
    if (!postMatches) {
        return
    }
    console.log(postMatches)
    const post = postMatches[1]

    const partsMatches = post.match(/^(\d{4})-(\d{2})-(\d{2})-(.+)[.]md$/)
    if (!partsMatches) {
        return
    }
    const year = partsMatches[1]
    const month = partsMatches[2]
    const day = partsMatches[3]
    const name = partsMatches[4]
    const url = `/blog/${year}/${month}/${day}/${name}`

    window.location.replace(url)
})();