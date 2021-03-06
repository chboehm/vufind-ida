/*global hierarchySettings, html_entity_decode, jqEscape, path, vufindString*/

var hierarchyID, recordID, htmlID;
var baseTreeSearchFullURL;


function getRecord(recordID)
{
  $.ajax({
    url: path + '/Hierarchy/GetRecord?' + $.param({id: recordID}),
    dataType: 'html',
    success: function(response) {
      if (response) {
        $('#hierarchyRecord').html(html_entity_decode(response));
        // Remove the old path highlighting
        $('#hierarchyTree a').removeClass("jstree-highlight");
        // Add Current path highlighting
        var jsTreeNode = $(":input[value='"+recordID+"']").parent();
        jsTreeNode.children("a").addClass("jstree-highlight");
        jsTreeNode.parents("li").children("a").addClass("jstree-highlight");
      }
    }
  });
}

function changeNoResultLabel(display)
{
  if (display) {
    $("#treeSearchNoResults").removeClass('hidden');
  } else {
    $("#treeSearchNoResults").addClass('hidden');
  }
}

function changeLimitReachedLabel(display)
{
  if (display) {
    $("#treeSearchLimitReached").removeClass('hidden');
  } else {
    $("#treeSearchLimitReached").addClass('hidden');
  }
}

function htmlEncodeId(id)
{
  return id.replace(/[^\wßäöüÄÖÜ]/g, "-"); // Also change Hierarchy/TreeRenderer/JSTree.php
}

var searchAjax = false;
function doTreeSearch()
{
    // todo: ajax loader icon

    var keyword = $("#treeSearchText").val(),
        type = $("#treeSearchType").val();

    if (0 < keyword.length) {

        if(searchAjax) {
            searchAjax.abort();
        }

        searchAjax = $.ajax({
            "url": path + '/Hierarchy/SearchTree?' + $.param({
                'lookfor': keyword,
                'hierarchyID': hierarchyID,
                'type': $("#treeSearchType").val()
            }) + "&format=true",
            'success': function(data) {

                var k = 0;

                //if (0 < data.results.length) {}
                for (; k < data.results.length; k++) {

                }
            }});
    }
    else {}
  /*$('#treeSearchLoadingImg').removeClass('hidden');
  var keyword = $("#treeSearchText").val();
  var type = $("#treeSearchType").val();
  if(keyword.length == 0) {
    $('#hierarchyTree').find('.jstree-search').removeClass('jstree-search');
    var tree = $('#hierarchyTree').jstree(true);
    tree.close_all();
    tree._open_to(htmlID);
    $('#treeSearchLoadingImg').addClass('hidden');
  } else {
    if(searchAjax) {
      searchAjax.abort();
    }
    searchAjax = $.ajax({
      "url" : path + '/Hierarchy/SearchTree?' + $.param({
        'lookfor': keyword,
        'hierarchyID': hierarchyID,
        'type': $("#treeSearchType").val()
      }) + "&format=true",
      'success': function(data) {
        if(data.results.length > 0) {
          $('#hierarchyTree').find('.jstree-search').removeClass('jstree-search');
          var tree = $('#hierarchyTree').jstree(true),
              i;
          //tree.close_all();
          //for(i=data.results.length;i--;) {
          //  var id = htmlEncodeId(data.results[i]);
          //  tree._open_to(id);
          //}
            //tree.close_node('1Bundesverband_def_addf');
            //tree.disable_node(tree.get_node('43374addf'));
            var nid='43374addf';
            tree.delete_node(tree.get_node(nid));
            tree.delete_node(nid);
          for(i=data.results.length;i--;) {
            var tid = htmlEncodeId(data.results[i]);
              //tree.delete_node(tid);
            //$('#hierarchyTree').find('#'+tid).addClass('jstree-search');
          }
          changeNoResultLabel(false);
          //changeLimitReachedLabel(data.limitReached);
        } else {
          changeNoResultLabel(true);
        }
          changeLimitReachedLabel(data.limitReached);
        $('#treeSearchLoadingImg').addClass('hidden');
      }
    });
  }*/
}

function buildJSONNodes(xml)
{
  var jsonNode = [];
  $(xml).children('item').each(function() {
    var content = $(this).children('content');
    var id = content.children("name[class='JSTreeID']");
    var name = content.children('name[href]');
      var ste = $(this).attr('state'), // "state" attribute
          children = $(this).children('item'), // Children elements (acc. to "xml" arg)
          hasChildren = 0 < children.length;
    jsonNode.push({
      'id': htmlEncodeId(id.text()),
      'text': name.text(),
      'li_attr': {
        'recordid': id.text()
      },
      'a_attr': {
        'href': content.children("hasChildren").attr("value")==="false" ? name.attr('href') : 'javascript:',
        'onclick': content.children("hasChildren").attr("value")==="false" ? '' : 'var anchorId = $(this).prev("i").click(); return false;',
        'title': name.text()
      },
      'type': name.attr('href').match(/\/Collection\//) ? 'collection' : 'record',
        // Build subtree if
        // a) children exist (in "xml") or
        // b) children do not exist in xml representation but node has attribute "state='closed'"
        children: hasChildren ? buildJSONNodes(this) : 'closed' === ste
    });
  });
  return jsonNode;
}

$(document).ready(function()
{
  // Code for the search button
  hierarchyID = $("#hierarchyTree").find(".hiddenHierarchyId")[0].value;
  recordID = $("#hierarchyTree").find(".hiddenRecordId")[0].value;
  htmlID = htmlEncodeId(recordID);
  var context = $("#hierarchyTree").find(".hiddenContext")[0].value;

  $("#hierarchyTree")
    .bind("ready.jstree", function (event, data) {
      var tree = $("#hierarchyTree").jstree(true);
      tree.select_node(htmlID);
      tree._open_to(htmlID);

      if (context == "Collection") {
        getRecord(recordID);
      }

      $("#hierarchyTree").bind('select_node.jstree', function(e, data) {
        if (context == "Record") {
          window.location.href = data.node.a_attr.href;
        } else {
          getRecord(data.node.li_attr.recordid);
        }
      });

      // Scroll to the current record
      if ($('#hierarchyTree').parents('#modal').length > 0) {
        var hTree = $('#hierarchyTree');
        var offsetTop = hTree.offset().top;
        var maxHeight = Math.max($(window).height() - offsetTop - 50, 200);
        hTree.css('max-height', maxHeight + 'px').css('overflow', 'auto');
        /* OM PHE not required
        hTree.animate({
          scrollTop: $('.jstree-clicked').offset().top - offsetTop + hTree.scrollTop() - 50
        }, 1500);
        */
      } else {
        /* OM PHE not required
        $('html,body').animate({
          scrollTop: $('.jstree-clicked').offset().top - 50
        }, 1500);
        */
      }
    })
    .jstree({
          'plugins': ['search', 'types'],
          'core': {
              check_callback:true,
              // Ajax mode (see http://www.jstree.com/docs/json/)
              'data': function (obj, cb) {
                  $.ajax({
                      'url': path + '/Hierarchy/GetTree',
                      'data': '#' === obj.id
                          ? {
                          'hierarchyID': hierarchyID,
                          'id': recordID,
                          'context': context,
                          'mode': 'Tree'
                      }
                          : {
                          // Load subtree
                          'hierarchyID': obj.id,
                          'subtree': 'true',
                          'id': recordID,
                          'context': context,
                          'mode': 'Tree'
                      },
                      'success': function (xml) {
                          var nodes = buildJSONNodes($(xml).find('root'));
                          cb.call(this, nodes);
                      }
                  });
              },
              'themes': {
                  'url': path + '/themes/bootstrap3/js/vendor/jsTree/themes/default/style.css'
              }
          },
          'types': {
              'record': {
                  'icon': 'fa fa-file'
              },
              'collection': {
                  'icon': 'fa fa-folder'
              }
          }
      });

  $('#treeSearch').removeClass('hidden');
  $('#treeSearch [type=submit]').click(doTreeSearch);
  $('#treeSearchText').keyup(function (e) {
    var code = (e.keyCode ? e.keyCode : e.which);
    if(code == 13 || $(this).val().length == 0) {
      doTreeSearch();
    }
  });
});