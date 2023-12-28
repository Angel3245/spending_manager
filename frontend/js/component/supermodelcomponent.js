class SuperModelComponent extends Fronty.ModelComponent {
    constructor(modelRenderer, model, htmlNodeId, listaCss) {
      super(modelRenderer,model,htmlNodeId);
      this.listaCss = listaCss;
    }

    import_css(urls){
        /* Basic checks */
        if(urls == '' || urls== [] || urls == [''] || urls == null || !Array.isArray(urls))
            return false
    
        var links = []
        /* Create as 'link' tags as elements of the list */
        for( let i = 0; i < urls.length; i++ ){
    
            /* Create and append attrs. Then, add them to 'link' */
            var t_link = document.createElement("link")
            t_link.setAttribute("rel","stylesheet")
            t_link.setAttribute("href",urls[i])
            /* Append to array of links */
            links.push(t_link)
        }
    
        /* Iterating in order to append each link to 'head' */
        links.forEach( link => {
            document.querySelector("head").appendChild(link)
        });
    
        return true
    }
    
    
    remove(urls){
         /* Basic checks */
        if(urls == '' || urls== [] || urls == [''] || urls == null || !Array.isArray(urls))
            return;
        
        /* Getting all links from 'head' */
        var t_head = document.getElementsByTagName("head")[0]
        var links = t_head.querySelectorAll("link")

        for(var link of links){
            if( urls.includes(link.getAttribute('href')) )
                link.parentNode.removeChild( link )
            
        }
        

    }
    
}