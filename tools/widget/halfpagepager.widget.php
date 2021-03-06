<?php

class HalfPagePager extends RoxWidget
{

    const block_links = 2;

    private $style;

    public function __construct($style = 'pull-right')
    {
        $this->style = $style;
    }

    /**
     * outputs a list of list links, to reflect paging
     *
     * @access public
     * @return void
     */
    public function render()
    {
        if ($this->pages < 2)
        {
            return;
        }
        echo $this->getHtml();
    }

    /**
     * return pagination button
     *
     * @param int $page - page number
     *
     * @access private
     * @return string
     */
    private function outputPage($page)
    {
        if ($page == $this->active_page)
        {
            return "<li class='active'><a>{$page}</a></li>\n";
        }
        else
        {
            return "<li>{$this->pager->outputLink($page, $page, $this->getWords()->getSilent('PagerGoToPage', $page))}</li>\n";
        }
    }

    /**
     * returns rather than renders the pager
     *
     * @access public
     * @return string
     */
    public function getHtml()
    {
        $return =<<<HTML
<div class="pages clearfix">
    <ul class="pagination pull-{$this->style}">
HTML;
        if ($this->active_page > 1)
        {
            if ($this->pages > self::block_links)
            {
                $return .= "<li>{$this->pager->outputLink(1, '&lt;&lt;', $this->getWords()->getSilent('PagerToFirstPage'))}</li>\n";
            }
            $return .= "<li>{$this->pager->outputLink($this->active_page - 1, '&lt;',$this->getWords()->getSilent('PagerToFirstPage'))}</li>\n";
        }
        else
        {
            if ($this->pages > self::block_links)
            {
                $return .= "<li class=\"disabled\"><a>&lt;&lt;</a></li>\n";
            }
            $return .= "<li class=\"disabled\"><a>&lt;</a></li>\n";
        }
        for ($i = 1; $i <= self::block_links && $i <= $this->pages; $i++)
        {
            $return .= $this->outputPage($i);
        }

        if ($this->pages > self::block_links)
        {
            $end_range = $this->pages - (self::block_links - 1);
            if ($end_range <= (self::block_links + 1))
            {
                $end_range = self::block_links + 1;
            }

            if (($this->active_page - 1) > (self::block_links + 1))
            {
                $return .= "<li class=\"disabled\"><a>...</a></li>\n";
            }

            if ($this->active_page >= self::block_links && $this->active_page <= $end_range)
            {
                for ($i = ($this->active_page - 1); $i <= ($this->active_page + 1); $i++)
                {
                    if (self::block_links < $i && $end_range > $i)
                    {
                        $return .= $this->outputPage($i);
                    }
                }

            }

            if ($end_range > ($this->active_page + 1) && $end_range > (self::block_links + 1))
            {
                $return .= "<li class=\"disabled\"><a>...</a></li>\n";
            }

            while ($end_range <= $this->pages)
            {
                $return .= $this->outputPage($end_range);
                $end_range++;
            }
        }

        if ($this->active_page < $this->pages)
        {
            $return .= "<li>{$this->pager->outputLink($this->active_page + 1, '&gt;', $this->getWords()->getSilent('PagerToNextPage'))}</li>\n";
            if ($this->pages > self::block_links)
            {
                $return .= "<li>{$this->pager->outputLink($this->pages, '&gt;&gt;', $this->getWords()->getSilent('PagerToLastPage'))}</li>\n";
            }
        }
        else
        {
            $return .= "<li class=\"disabled\"><a>&gt;</a></li>\n";
            if ($this->pages > self::block_links)
            {
                $return .= "<li class=\"disabled\"><a>&gt;&gt;</a></li>\n";
            }
        }
        $return .= <<<HTML
        </ul>
</div>
HTML;
        return $return;
    }
}
