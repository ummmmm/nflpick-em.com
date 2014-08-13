<?php
function Module_Head()
{
?>
<script type="text/javascript" language="javascript">
$( document ).ready( function(){
	var week, gameDay, awayTeam, homeTeam, gameTime;
	var string = '';
	
	$( '.colhead' ).each(function()
	{
		week	= $(this).parent().children( 'tr' ).children( 'td:eq(0)' ).children( 'a:eq(0)' ).attr( 'name' );
		gameDay = $(this).children( 'td:eq(0)' ).text();
		$( '#test' ).append( '<br /><b>Week ' + week + '</b><br />' );
		
		$( this ).nextUntil( '.colhead' ).each( function()
		{
			if ( $( this ).text().match( /Bye:/ ) )
			{
				$( '#test' ).append( '<b>' + $( this ).text() + '</b><br />' );
				return;
			}

			awayTeam = $( this ).children( 'td:eq(0)' ).children( 'a:eq(0)' ).text();
			homeTeam = $( this ).children( 'td:eq(0)' ).children( 'a:eq(1)' ).text();
			gameTime = $( this ).children( 'td:eq(1)' ).text();
			
			$( '#test' ).append( awayTeam + ' vs. ' + homeTeam + ' @ ' + gameDay + ' ' + gameTime + '<br />' );
			
			string += week + '::' + awayTeam + '::' + homeTeam + '::' + gameDay + ' ' + gameTime + '|';
		} );
	} );

	$.fn.json( 'games_add', 'games=' + encodeURIComponent( string.slice( 0, -1 ) ), function( response )
	{
		if ( !response.success )
		{
			return alert( response.error_message );
		}
		
		alert( response.data );
	} );
});
</script>
<?php
	return true;
}

function Module_Content( &$db, &$user )
{
	print '<h1>Games Insert</h1>';

	return true;
}
?>
<div id="test"></div>
<div style="display:none;">

<table class="tablehead" cellpadding="3" cellspacing="1"><tbody><tr class="stathead"><td colspan="5"><a name="1" style="color:#FFFFFF;"></a>Week 1<span><a href="#top">back to top �</a></span></td></tr>
<tr class="colhead">
<td width="170">THU, SEP 5</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-33 team-28-7"><td><a href="http://espn.go.com/nfl/team/_/name/bal/baltimore-ravens">Baltimore</a> at <a href="http://espn.go.com/nfl/team/_/name/den/denver-broncos">Denver</a></td>
<td>8:30 PM</td>
<td align="center">NBC</td><td><a href="http://www.stubhub.com/denver-broncos-denver-sports-authority-field-at-mile-high-5-9-2013-4270595?gcid=C12289x445&amp;keyword=NFL+Schedule+Denver+Broncos+20130905">2,821 available from $132</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/7">Sports Authority Field at Mile High</a></td>
</tr>
<tr class="colhead">
<td width="170">SUN, SEP 8</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-17 team-28-2"><td><a href="http://espn.go.com/nfl/team/_/name/ne/new-england-patriots">New England</a> at <a href="http://espn.go.com/nfl/team/_/name/buf/buffalo-bills">Buffalo</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/buffalo-bills-orchard-park-ralph-wilson-stadium-8-9-2013-4270658?gcid=C12289x445&amp;keyword=NFL+Schedule+Buffalo+Bills+20130908">5,146 available from $20</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/2">Ralph Wilson Stadium</a></td>
</tr>
<tr class="evenrow team-28-4 team-28-3"><td><a href="http://espn.go.com/nfl/team/_/name/cin/cincinnati-bengals">Cincinnati</a> at <a href="http://espn.go.com/nfl/team/_/name/chi/chicago-bears">Chicago</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/chicago-bears-chicago-soldier-field-8-9-2013-4270597?gcid=C12289x445&amp;keyword=NFL+Schedule+Chicago+Bears+20130908">3,562 available from $111</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/3">Soldier Field</a></td>
</tr>
<tr class="oddrow team-28-15 team-28-5"><td><a href="http://espn.go.com/nfl/team/_/name/mia/miami-dolphins">Miami</a> at <a href="http://espn.go.com/nfl/team/_/name/cle/cleveland-browns">Cleveland</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/cleveland-browns-cleveland-firstenergy-stadium-8-9-2013-4271232?gcid=C12289x445&amp;keyword=NFL+Schedule+Cleveland+Browns+20130908">2,935 available from $61</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/5">FirstEnergy Stadium</a></td>
</tr>
<tr class="evenrow team-28-1 team-28-18"><td><a href="http://espn.go.com/nfl/team/_/name/atl/atlanta-falcons">Atlanta</a> at <a href="http://espn.go.com/nfl/team/_/name/no/new-orleans-saints">New Orleans</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/new-orleans-saints-new-orleans-mercedes-benz-superdome-8-9-2013-4271178?gcid=C12289x445&amp;keyword=NFL+Schedule+New+Orleans+Saints+20130908">2,305 available from $193</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/18">Mercedes-Benz Superdome</a></td>
</tr>
<tr class="oddrow team-28-27 team-28-20"><td><a href="http://espn.go.com/nfl/team/_/name/tb/tampa-bay-buccaneers">Tampa Bay</a> at <a href="http://espn.go.com/nfl/team/_/name/nyj/new-york-jets">NY Jets</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/new-york-jets-east-rutherford-metlife-stadium-8-9-2013-4270600?gcid=C12289x445&amp;keyword=NFL+Schedule+New+York+Jets+20130908">6,416 available from $35</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/20">MetLife Stadium</a></td>
</tr>
<tr class="evenrow team-28-10 team-28-23"><td><a href="http://espn.go.com/nfl/team/_/name/ten/tennessee-titans">Tennessee</a> at <a href="http://espn.go.com/nfl/team/_/name/pit/pittsburgh-steelers">Pittsburgh</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/pittsburgh-steelers-pittsburgh-heinz-field-8-9-2013-4271244?gcid=C12289x445&amp;keyword=NFL+Schedule+Pittsburgh+Steelers+20130908">3,909 available from $57</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/23">Heinz Field</a></td>
</tr>
<tr class="oddrow team-28-16 team-28-8"><td><a href="http://espn.go.com/nfl/team/_/name/min/minnesota-vikings">Minnesota</a> at <a href="http://espn.go.com/nfl/team/_/name/det/detroit-lions">Detroit</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/detroit-lions-detroit-ford-field-8-9-2013-4271129?gcid=C12289x445&amp;keyword=NFL+Schedule+Detroit+Lions+20130908">9,207 available from $41</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/8">Ford Field</a></td>
</tr>
<tr class="evenrow team-28-13 team-28-11"><td><a href="http://espn.go.com/nfl/team/_/name/oak/oakland-raiders">Oakland</a> at <a href="http://espn.go.com/nfl/team/_/name/ind/indianapolis-colts">Indianapolis</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/indianapolis-colts-indianapolis-lucas-oil-stadium-8-9-2013-4271249?gcid=C12289x445&amp;keyword=NFL+Schedule+Indianapolis+Colts+20130908">4,346 available from $21</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/11">Lucas Oil Stadium</a></td>
</tr>
<tr class="oddrow team-28-26 team-28-29"><td><a href="http://espn.go.com/nfl/team/_/name/sea/seattle-seahawks">Seattle</a> at <a href="http://espn.go.com/nfl/team/_/name/car/carolina-panthers">Carolina</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/carolina-panthers-charlotte-bank-of-america-stadium-8-9-2013-4270701?gcid=C12289x445&amp;keyword=NFL+Schedule+Carolina+Panthers+20130908">4,594 available from $46</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/29">Bank of America Stadium</a></td>
</tr>
<tr class="evenrow team-28-12 team-28-30"><td><a href="http://espn.go.com/nfl/team/_/name/kc/kansas-city-chiefs">Kansas City</a> at <a href="http://espn.go.com/nfl/team/_/name/jac/jacksonville-jaguars">Jacksonville</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/jacksonville-jaguars-jacksonville-everbank-field-8-9-2013-4270678?gcid=C12289x445&amp;keyword=NFL+Schedule+Jacksonville+Jaguars+20130908">2,616 available from $42</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/30">EverBank Field</a></td>
</tr>
<tr class="oddrow team-28-22 team-28-14"><td><a href="http://espn.go.com/nfl/team/_/name/ari/arizona-cardinals">Arizona</a> at <a href="http://espn.go.com/nfl/team/_/name/stl/st.-louis-rams">St. Louis</a></td>
<td>4:25 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/st-louis-rams-saint-louis-edward-jones-dome-8-9-2013-4271274?gcid=C12289x445&amp;keyword=NFL+Schedule+St.+Louis+Rams+20130908">7,442 available from $19</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/14">Edward Jones Dome</a></td>
</tr>
<tr class="evenrow team-28-9 team-28-25"><td><a href="http://espn.go.com/nfl/team/_/name/gb/green-bay-packers">Green Bay</a> at <a href="http://espn.go.com/nfl/team/_/name/sf/san-francisco-49ers">San Francisco</a></td>
<td>4:25 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/san-francisco-49ers-san-francisco-candlestick-park-8-9-2013-4270594?gcid=C12289x445&amp;keyword=NFL+Schedule+San+Francisco+49ers+20130908">8,504 available from $113</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/25">Candlestick Park</a></td>
</tr>
<tr class="oddrow team-28-19 team-28-6"><td><a href="http://espn.go.com/nfl/team/_/name/nyg/new-york-giants">NY Giants</a> at <a href="http://espn.go.com/nfl/team/_/name/dal/dallas-cowboys">Dallas</a></td>
<td>8:30 PM</td>
<td align="center">NBC</td><td><a href="http://www.stubhub.com/dallas-cowboys-arlington-cowboys-stadium-8-9-2013-4270641?gcid=C12289x445&amp;keyword=NFL+Schedule+Dallas+Cowboys+20130908">13,618 available from $41</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/6">AT&amp;T Stadium</a></td>
</tr>
<tr class="colhead">
<td width="170">MON, SEP 9</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-21 team-28-28"><td><a href="http://espn.go.com/nfl/team/_/name/phi/philadelphia-eagles">Philadelphia</a> at <a href="http://espn.go.com/nfl/team/_/name/wsh/washington-redskins">Washington</a></td>
<td>7:00 PM</td>
<td align="center"><a href="http://sports.espn.go.com/espntv/espnNetwork?networkID=1"><img src="http://a.espncdn.com/i/scoreboard/networkLogo_espn.gif" width="55" height="14"></a><a href="http://espn.go.com/watchespn/?channel=espn&amp;launchPlayer=true"><img src="http://a.espncdn.com/i/scoreboard/watchespn-55x12.png"></a></td><td><a href="http://www.stubhub.com/washington-redskins-landover-fedexfield-9-9-2013-4271212?gcid=C12289x445&amp;keyword=NFL+Schedule+Washington+Redskins+20130909">6,793 available from $33</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/28">FedEx Field</a></td>
</tr>
<tr class="evenrow team-28-34 team-28-24"><td><a href="http://espn.go.com/nfl/team/_/name/hou/houston-texans">Houston</a> at <a href="http://espn.go.com/nfl/team/_/name/sd/san-diego-chargers">San Diego</a></td>
<td>10:15 PM</td>
<td align="center"><a href="http://sports.espn.go.com/espntv/espnNetwork?networkID=1"><img src="http://a.espncdn.com/i/scoreboard/networkLogo_espn.gif" width="55" height="14"></a><a href="http://espn.go.com/watchespn/?channel=espn&amp;launchPlayer=true"><img src="http://a.espncdn.com/i/scoreboard/watchespn-55x12.png"></a></td><td><a href="http://www.stubhub.com/san-diego-chargers-san-diego-qualcomm-stadium-9-9-2013-4270609?gcid=C12289x445&amp;keyword=NFL+Schedule+San+Diego+Chargers+20130909">7,849 available from $45</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/24">Qualcomm Stadium</a></td>
</tr>
</tbody></table><table class="tablehead" cellpadding="3" cellspacing="1"><tbody><tr class="stathead"><td colspan="5"><a name="2" style="color:#FFFFFF;"></a>Week 2<span><a href="#top">back to top �</a></span></td></tr>
<tr class="colhead">
<td width="170">THU, SEP 12</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-20 team-28-17"><td><a href="http://espn.go.com/nfl/team/_/name/nyj/new-york-jets">NY Jets</a> at <a href="http://espn.go.com/nfl/team/_/name/ne/new-england-patriots">New England</a></td>
<td>8:25 PM</td>
<td align="center">NFL</td><td><a href="http://www.stubhub.com/new-england-patriots-foxborough-gillette-stadium-12-9-2013-4270627?gcid=C12289x445&amp;keyword=NFL+Schedule+New+England+Patriots+20130912">3,451 available from $123</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/17">Gillette Stadium</a></td>
</tr>
<tr class="colhead">
<td width="170">SUN, SEP 15</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-14 team-28-1"><td><a href="http://espn.go.com/nfl/team/_/name/stl/st.-louis-rams">St. Louis</a> at <a href="http://espn.go.com/nfl/team/_/name/atl/atlanta-falcons">Atlanta</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/atlanta-falcons-atlanta-georgia-dome-15-9-2013-4271146?gcid=C12289x445&amp;keyword=NFL+Schedule+Atlanta+Falcons+20130915">6,799 available from $24</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/1">Georgia Dome</a></td>
</tr>
<tr class="evenrow team-28-29 team-28-2"><td><a href="http://espn.go.com/nfl/team/_/name/car/carolina-panthers">Carolina</a> at <a href="http://espn.go.com/nfl/team/_/name/buf/buffalo-bills">Buffalo</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/buffalo-bills-orchard-park-ralph-wilson-stadium-15-9-2013-4270661?gcid=C12289x445&amp;keyword=NFL+Schedule+Buffalo+Bills+20130915">4,285 available from $20</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/2">Ralph Wilson Stadium</a></td>
</tr>
<tr class="oddrow team-28-16 team-28-3"><td><a href="http://espn.go.com/nfl/team/_/name/min/minnesota-vikings">Minnesota</a> at <a href="http://espn.go.com/nfl/team/_/name/chi/chicago-bears">Chicago</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/chicago-bears-chicago-soldier-field-15-9-2013-4271113?gcid=C12289x445&amp;keyword=NFL+Schedule+Chicago+Bears+20130915">3,542 available from $111</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/3">Soldier Field</a></td>
</tr>
<tr class="evenrow team-28-28 team-28-9"><td><a href="http://espn.go.com/nfl/team/_/name/wsh/washington-redskins">Washington</a> at <a href="http://espn.go.com/nfl/team/_/name/gb/green-bay-packers">Green Bay</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/green-bay-packers-green-bay-lambeau-field-15-9-2013-4270617?gcid=C12289x445&amp;keyword=NFL+Schedule+Green+Bay+Packers+20130915">1,737 available from $93</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/9">Lambeau Field</a></td>
</tr>
<tr class="oddrow team-28-15 team-28-11"><td><a href="http://espn.go.com/nfl/team/_/name/mia/miami-dolphins">Miami</a> at <a href="http://espn.go.com/nfl/team/_/name/ind/indianapolis-colts">Indianapolis</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/indianapolis-colts-indianapolis-lucas-oil-stadium-15-9-2013-4271252?gcid=C12289x445&amp;keyword=NFL+Schedule+Indianapolis+Colts+20130915">4,438 available from $57</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/11">Lucas Oil Stadium</a></td>
</tr>
<tr class="evenrow team-28-6 team-28-12"><td><a href="http://espn.go.com/nfl/team/_/name/dal/dallas-cowboys">Dallas</a> at <a href="http://espn.go.com/nfl/team/_/name/kc/kansas-city-chiefs">Kansas City</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/kansas-city-chiefs-kansas-city-arrowhead-stadium-15-9-2013-4271172?gcid=C12289x445&amp;keyword=NFL+Schedule+Kansas+City+Chiefs+20130915">4,568 available from $71</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/12">Arrowhead Stadium</a></td>
</tr>
<tr class="oddrow team-28-5 team-28-33"><td><a href="http://espn.go.com/nfl/team/_/name/cle/cleveland-browns">Cleveland</a> at <a href="http://espn.go.com/nfl/team/_/name/bal/baltimore-ravens">Baltimore</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/baltimore-ravens-baltimore-m-t-bank-stadium-15-9-2013-4271184?gcid=C12289x445&amp;keyword=NFL+Schedule+Baltimore+Ravens+20130915">2,795 available from $129</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/33">M&amp;T Bank Stadium</a></td>
</tr>
<tr class="evenrow team-28-10 team-28-34"><td><a href="http://espn.go.com/nfl/team/_/name/ten/tennessee-titans">Tennessee</a> at <a href="http://espn.go.com/nfl/team/_/name/hou/houston-texans">Houston</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/houston-texans-houston-reliant-stadium-15-9-2013-4271233?gcid=C12289x445&amp;keyword=NFL+Schedule+Houston+Texans+20130915">4,352 available from $41</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/34">Reliant Stadium</a></td>
</tr>
<tr class="oddrow team-28-24 team-28-21"><td><a href="http://espn.go.com/nfl/team/_/name/sd/san-diego-chargers">San Diego</a> at <a href="http://espn.go.com/nfl/team/_/name/phi/philadelphia-eagles">Philadelphia</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/philadelphia-eagles-philadelphia-lincoln-financial-field-15-9-2013-4270649?gcid=C12289x445&amp;keyword=NFL+Schedule+Philadelphia+Eagles+20130915">4,447 available from $60</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/21">Lincoln Financial Field</a></td>
</tr>
<tr class="evenrow team-28-8 team-28-22"><td><a href="http://espn.go.com/nfl/team/_/name/det/detroit-lions">Detroit</a> at <a href="http://espn.go.com/nfl/team/_/name/ari/arizona-cardinals">Arizona</a></td>
<td>4:05 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/arizona-cardinals-glendale-university-of-phoenix-stadium-15-9-2013-4271221?gcid=C12289x445&amp;keyword=NFL+Schedule+Arizona+Cardinals+20130915">6,395 available from $5</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/22">U of Phoenix Stadium</a></td>
</tr>
<tr class="oddrow team-28-18 team-28-27"><td><a href="http://espn.go.com/nfl/team/_/name/no/new-orleans-saints">New Orleans</a> at <a href="http://espn.go.com/nfl/team/_/name/tb/tampa-bay-buccaneers">Tampa Bay</a></td>
<td>4:05 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/tampa-bay-buccaneers-tampa-raymond-james-stadium-15-9-2013-4271266?gcid=C12289x445&amp;keyword=NFL+Schedule+Tampa+Bay+Buccaneers+20130915">3,785 available from $41</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/27">Raymond James Stadium</a></td>
</tr>
<tr class="evenrow team-28-30 team-28-13"><td><a href="http://espn.go.com/nfl/team/_/name/jac/jacksonville-jaguars">Jacksonville</a> at <a href="http://espn.go.com/nfl/team/_/name/oak/oakland-raiders">Oakland</a></td>
<td>4:25 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/oakland-raiders-oakland-o-co-coliseum-15-9-2013-4271147?gcid=C12289x445&amp;keyword=NFL+Schedule+Oakland+Raiders+20130915">4,715 available from $27</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/13">O.co Coliseum</a></td>
</tr>
<tr class="oddrow team-28-7 team-28-19"><td><a href="http://espn.go.com/nfl/team/_/name/den/denver-broncos">Denver</a> at <a href="http://espn.go.com/nfl/team/_/name/nyg/new-york-giants">NY Giants</a></td>
<td>4:25 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/new-york-giants-east-rutherford-metlife-stadium-15-9-2013-4271171?gcid=C12289x445&amp;keyword=NFL+Schedule+New+York+Giants+20130915">4,564 available from $49</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/19">MetLife Stadium</a></td>
</tr>
<tr class="evenrow team-28-25 team-28-26"><td><a href="http://espn.go.com/nfl/team/_/name/sf/san-francisco-49ers">San Francisco</a> at <a href="http://espn.go.com/nfl/team/_/name/sea/seattle-seahawks">Seattle</a></td>
<td>8:30 PM</td>
<td align="center">NBC</td><td><a href="http://www.stubhub.com/seattle-seahawks-seattle-centurylink-field-15-9-2013-4271126?gcid=C12289x445&amp;keyword=NFL+Schedule+Seattle+Seahawks+20130915">3,381 available from $113</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/26">CenturyLink Field</a></td>
</tr>
<tr class="colhead">
<td width="170">MON, SEP 16</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-23 team-28-4"><td><a href="http://espn.go.com/nfl/team/_/name/pit/pittsburgh-steelers">Pittsburgh</a> at <a href="http://espn.go.com/nfl/team/_/name/cin/cincinnati-bengals">Cincinnati</a></td>
<td>8:30 PM</td>
<td align="center"><a href="http://sports.espn.go.com/espntv/espnNetwork?networkID=1"><img src="http://a.espncdn.com/i/scoreboard/networkLogo_espn.gif" width="55" height="14"></a><a href="http://espn.go.com/watchespn/?channel=espn&amp;launchPlayer=true"><img src="http://a.espncdn.com/i/scoreboard/watchespn-55x12.png"></a></td><td><a href="http://www.stubhub.com/cincinnati-bengals-cincinnati-paul-brown-stadium-16-9-2013-4271206?gcid=C12289x445&amp;keyword=NFL+Schedule+Cincinnati+Bengals+20130916">10,030 available from $42</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/4">Paul Brown Stadium</a></td>
</tr>
</tbody></table><table class="tablehead" cellpadding="3" cellspacing="1"><tbody><tr class="stathead"><td colspan="5"><a name="3" style="color:#FFFFFF;"></a>Week 3<span><a href="#top">back to top �</a></span></td></tr>
<tr class="colhead">
<td width="170">THU, SEP 19</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-12 team-28-21"><td><a href="http://espn.go.com/nfl/team/_/name/kc/kansas-city-chiefs">Kansas City</a> at <a href="http://espn.go.com/nfl/team/_/name/phi/philadelphia-eagles">Philadelphia</a></td>
<td>8:25 PM</td>
<td align="center">NFL</td><td><a href="http://www.stubhub.com/philadelphia-eagles-philadelphia-lincoln-financial-field-19-9-2013-4271195?gcid=C12289x445&amp;keyword=NFL+Schedule+Philadelphia+Eagles+20130919">3,999 available from $60</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/21">Lincoln Financial Field</a></td>
</tr>
<tr class="colhead">
<td width="170">SUN, SEP 22</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-9 team-28-4"><td><a href="http://espn.go.com/nfl/team/_/name/gb/green-bay-packers">Green Bay</a> at <a href="http://espn.go.com/nfl/team/_/name/cin/cincinnati-bengals">Cincinnati</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/cincinnati-bengals-cincinnati-paul-brown-stadium-22-9-2013-4271208?gcid=C12289x445&amp;keyword=NFL+Schedule+Cincinnati+Bengals+20130922">8,468 available from $42</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/4">Paul Brown Stadium</a></td>
</tr>
<tr class="evenrow team-28-14 team-28-6"><td><a href="http://espn.go.com/nfl/team/_/name/stl/st.-louis-rams">St. Louis</a> at <a href="http://espn.go.com/nfl/team/_/name/dal/dallas-cowboys">Dallas</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/dallas-cowboys-arlington-cowboys-stadium-22-9-2013-4271160?gcid=C12289x445&amp;keyword=NFL+Schedule+Dallas+Cowboys+20130922">15,890 available from $21</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/6">AT&amp;T Stadium</a></td>
</tr>
<tr class="oddrow team-28-24 team-28-10"><td><a href="http://espn.go.com/nfl/team/_/name/sd/san-diego-chargers">San Diego</a> at <a href="http://espn.go.com/nfl/team/_/name/ten/tennessee-titans">Tennessee</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/tennessee-titans-nashville-lp-field-22-9-2013-4271263?gcid=C12289x445&amp;keyword=NFL+Schedule+Tennessee+Titans+20130922">4,652 available from $24</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/10">LP Field</a></td>
</tr>
<tr class="evenrow team-28-5 team-28-16"><td><a href="http://espn.go.com/nfl/team/_/name/cle/cleveland-browns">Cleveland</a> at <a href="http://espn.go.com/nfl/team/_/name/min/minnesota-vikings">Minnesota</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/minnesota-vikings-minneapolis-hubert-h--humphrey-metrodome-22-9-2013-4271149?gcid=C12289x445&amp;keyword=NFL+Schedule+Minnesota+Vikings+20130922">6,093 available from $19</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/16">Mall of America Field</a></td>
</tr>
<tr class="oddrow team-28-27 team-28-17"><td><a href="http://espn.go.com/nfl/team/_/name/tb/tampa-bay-buccaneers">Tampa Bay</a> at <a href="http://espn.go.com/nfl/team/_/name/ne/new-england-patriots">New England</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/new-england-patriots-foxborough-gillette-stadium-22-9-2013-4271143?gcid=C12289x445&amp;keyword=NFL+Schedule+New+England+Patriots+20130922">2,145 available from $145</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/17">Gillette Stadium</a></td>
</tr>
<tr class="evenrow team-28-22 team-28-18"><td><a href="http://espn.go.com/nfl/team/_/name/ari/arizona-cardinals">Arizona</a> at <a href="http://espn.go.com/nfl/team/_/name/no/new-orleans-saints">New Orleans</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/new-orleans-saints-new-orleans-mercedes-benz-superdome-22-9-2013-4271180?gcid=C12289x445&amp;keyword=NFL+Schedule+New+Orleans+Saints+20130922">3,158 available from $69</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/18">Mercedes-Benz Superdome</a></td>
</tr>
<tr class="oddrow team-28-8 team-28-28"><td><a href="http://espn.go.com/nfl/team/_/name/det/detroit-lions">Detroit</a> at <a href="http://espn.go.com/nfl/team/_/name/wsh/washington-redskins">Washington</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/washington-redskins-landover-fedexfield-22-9-2013-4271215?gcid=C12289x445&amp;keyword=NFL+Schedule+Washington+Redskins+20130922">7,717 available from $29</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/28">FedEx Field</a></td>
</tr>
<tr class="evenrow team-28-19 team-28-29"><td><a href="http://espn.go.com/nfl/team/_/name/nyg/new-york-giants">NY Giants</a> at <a href="http://espn.go.com/nfl/team/_/name/car/carolina-panthers">Carolina</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/carolina-panthers-charlotte-bank-of-america-stadium-22-9-2013-4270703?gcid=C12289x445&amp;keyword=NFL+Schedule+Carolina+Panthers+20130922">5,835 available from $48</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/29">Bank of America Stadium</a></td>
</tr>
<tr class="oddrow team-28-34 team-28-33"><td><a href="http://espn.go.com/nfl/team/_/name/hou/houston-texans">Houston</a> at <a href="http://espn.go.com/nfl/team/_/name/bal/baltimore-ravens">Baltimore</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/baltimore-ravens-baltimore-m-t-bank-stadium-22-9-2013-4271187?gcid=C12289x445&amp;keyword=NFL+Schedule+Baltimore+Ravens+20130922">2,080 available from $169</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/33">M&amp;T Bank Stadium</a></td>
</tr>
<tr class="evenrow team-28-1 team-28-15"><td><a href="http://espn.go.com/nfl/team/_/name/atl/atlanta-falcons">Atlanta</a> at <a href="http://espn.go.com/nfl/team/_/name/mia/miami-dolphins">Miami</a></td>
<td>4:05 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/miami-dolphins-miami-gardens-sun-life-stadium-22-9-2013-4271157?gcid=C12289x445&amp;keyword=NFL+Schedule+Miami+Dolphins+20130922">6,222 available from $35</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/15">Sun Life Stadium</a></td>
</tr>
<tr class="oddrow team-28-2 team-28-20"><td><a href="http://espn.go.com/nfl/team/_/name/buf/buffalo-bills">Buffalo</a> at <a href="http://espn.go.com/nfl/team/_/name/nyj/new-york-jets">NY Jets</a></td>
<td>4:25 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/new-york-jets-east-rutherford-metlife-stadium-22-9-2013-4271114?gcid=C12289x445&amp;keyword=NFL+Schedule+New+York+Jets+20130922">7,833 available from $35</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/20">MetLife Stadium</a></td>
</tr>
<tr class="evenrow team-28-11 team-28-25"><td><a href="http://espn.go.com/nfl/team/_/name/ind/indianapolis-colts">Indianapolis</a> at <a href="http://espn.go.com/nfl/team/_/name/sf/san-francisco-49ers">San Francisco</a></td>
<td>4:25 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/san-francisco-49ers-san-francisco-candlestick-park-22-9-2013-4271107?gcid=C12289x445&amp;keyword=NFL+Schedule+San+Francisco+49ers+20130922">11,414 available from $94</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/25">Candlestick Park</a></td>
</tr>
<tr class="oddrow team-28-30 team-28-26"><td><a href="http://espn.go.com/nfl/team/_/name/jac/jacksonville-jaguars">Jacksonville</a> at <a href="http://espn.go.com/nfl/team/_/name/sea/seattle-seahawks">Seattle</a></td>
<td>4:25 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/seattle-seahawks-seattle-centurylink-field-22-9-2013-4270606?gcid=C12289x445&amp;keyword=NFL+Schedule+Seattle+Seahawks+20130922">4,649 available from $75</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/26">CenturyLink Field</a></td>
</tr>
<tr class="evenrow team-28-3 team-28-23"><td><a href="http://espn.go.com/nfl/team/_/name/chi/chicago-bears">Chicago</a> at <a href="http://espn.go.com/nfl/team/_/name/pit/pittsburgh-steelers">Pittsburgh</a></td>
<td>8:30 PM</td>
<td align="center">NBC</td><td><a href="http://www.stubhub.com/pittsburgh-steelers-pittsburgh-heinz-field-22-9-2013-4271245?gcid=C12289x445&amp;keyword=NFL+Schedule+Pittsburgh+Steelers+20130922">3,488 available from $58</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/23">Heinz Field</a></td>
</tr>
<tr class="colhead">
<td width="170">MON, SEP 23</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-13 team-28-7"><td><a href="http://espn.go.com/nfl/team/_/name/oak/oakland-raiders">Oakland</a> at <a href="http://espn.go.com/nfl/team/_/name/den/denver-broncos">Denver</a></td>
<td>8:30 PM</td>
<td align="center"><a href="http://sports.espn.go.com/espntv/espnNetwork?networkID=1"><img src="http://a.espncdn.com/i/scoreboard/networkLogo_espn.gif" width="55" height="14"></a><a href="http://espn.go.com/watchespn/?channel=espn&amp;launchPlayer=true"><img src="http://a.espncdn.com/i/scoreboard/watchespn-55x12.png"></a></td><td><a href="http://www.stubhub.com/denver-broncos-denver-sports-authority-field-at-mile-high-23-9-2013-4271111?gcid=C12289x445&amp;keyword=NFL+Schedule+Denver+Broncos+20130923">2,944 available from $68</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/7">Sports Authority Field at Mile High</a></td>
</tr>
</tbody></table><table class="tablehead" cellpadding="3" cellspacing="1"><tbody><tr class="stathead"><td colspan="5"><a name="4" style="color:#FFFFFF;"></a>Week 4<span><a href="#top">back to top �</a></span></td></tr>
<tr class="colhead">
<td width="170">THU, SEP 26</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-25 team-28-14"><td><a href="http://espn.go.com/nfl/team/_/name/sf/san-francisco-49ers">San Francisco</a> at <a href="http://espn.go.com/nfl/team/_/name/stl/st.-louis-rams">St. Louis</a></td>
<td>8:25 PM</td>
<td align="center">NFL</td><td><a href="http://www.stubhub.com/st-louis-rams-saint-louis-edward-jones-dome-26-9-2013-4270697?gcid=C12289x445&amp;keyword=NFL+Schedule+St.+Louis+Rams+20130926">10,079 available from $41</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/14">Edward Jones Dome</a></td>
</tr>
<tr class="colhead">
<td width="170">SUN, SEP 29</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-33 team-28-2"><td><a href="http://espn.go.com/nfl/team/_/name/bal/baltimore-ravens">Baltimore</a> at <a href="http://espn.go.com/nfl/team/_/name/buf/buffalo-bills">Buffalo</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/buffalo-bills-orchard-park-ralph-wilson-stadium-29-9-2013-4270663?gcid=C12289x445&amp;keyword=NFL+Schedule+Buffalo+Bills+20130929">5,588 available from $60</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/2">Ralph Wilson Stadium</a></td>
</tr>
<tr class="evenrow team-28-4 team-28-5"><td><a href="http://espn.go.com/nfl/team/_/name/cin/cincinnati-bengals">Cincinnati</a> at <a href="http://espn.go.com/nfl/team/_/name/cle/cleveland-browns">Cleveland</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/cleveland-browns-cleveland-firstenergy-stadium-29-9-2013-4270657?gcid=C12289x445&amp;keyword=NFL+Schedule+Cleveland+Browns+20130929">4,338 available from $34</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/5">FirstEnergy Stadium</a></td>
</tr>
<tr class="oddrow team-28-3 team-28-8"><td><a href="http://espn.go.com/nfl/team/_/name/chi/chicago-bears">Chicago</a> at <a href="http://espn.go.com/nfl/team/_/name/det/detroit-lions">Detroit</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/detroit-lions-detroit-ford-field-29-9-2013-4271131?gcid=C12289x445&amp;keyword=NFL+Schedule+Detroit+Lions+20130929">11,845 available from $64</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/8">Ford Field</a></td>
</tr>
<tr class="evenrow team-28-19 team-28-12"><td><a href="http://espn.go.com/nfl/team/_/name/nyg/new-york-giants">NY Giants</a> at <a href="http://espn.go.com/nfl/team/_/name/kc/kansas-city-chiefs">Kansas City</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/kansas-city-chiefs-kansas-city-arrowhead-stadium-29-9-2013-4271182?gcid=C12289x445&amp;keyword=NFL+Schedule+Kansas+City+Chiefs+20130929">11,870 available from $28</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/12">Arrowhead Stadium</a></td>
</tr>
<tr class="oddrow team-28-23 team-28-16"><td><a href="http://espn.go.com/nfl/team/_/name/pit/pittsburgh-steelers">Pittsburgh</a> at <a href="http://espn.go.com/nfl/team/_/name/min/minnesota-vikings">Minnesota</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/minnesota-vikings-tickets/?gcid=C12289x445&amp;keyword=NFL+Schedule+Minnesota+Vikings+20130929">Buy on StubHub</a></td>
<td>Wembley Stadium</td>
</tr>
<tr class="evenrow team-28-22 team-28-27"><td><a href="http://espn.go.com/nfl/team/_/name/ari/arizona-cardinals">Arizona</a> at <a href="http://espn.go.com/nfl/team/_/name/tb/tampa-bay-buccaneers">Tampa Bay</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/tampa-bay-buccaneers-tampa-raymond-james-stadium-29-9-2013-4270686?gcid=C12289x445&amp;keyword=NFL+Schedule+Tampa+Bay+Buccaneers+20130929">5,477 available from $27</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/27">Raymond James Stadium</a></td>
</tr>
<tr class="oddrow team-28-11 team-28-30"><td><a href="http://espn.go.com/nfl/team/_/name/ind/indianapolis-colts">Indianapolis</a> at <a href="http://espn.go.com/nfl/team/_/name/jac/jacksonville-jaguars">Jacksonville</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/jacksonville-jaguars-jacksonville-everbank-field-29-9-2013-4271248?gcid=C12289x445&amp;keyword=NFL+Schedule+Jacksonville+Jaguars+20130929">3,658 available from $45</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/30">EverBank Field</a></td>
</tr>
<tr class="evenrow team-28-26 team-28-34"><td><a href="http://espn.go.com/nfl/team/_/name/sea/seattle-seahawks">Seattle</a> at <a href="http://espn.go.com/nfl/team/_/name/hou/houston-texans">Houston</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/houston-texans-houston-reliant-stadium-29-9-2013-4270662?gcid=C12289x445&amp;keyword=NFL+Schedule+Houston+Texans+20130929">3,816 available from $41</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/34">Reliant Stadium</a></td>
</tr>
<tr class="oddrow team-28-20 team-28-10"><td><a href="http://espn.go.com/nfl/team/_/name/nyj/new-york-jets">NY Jets</a> at <a href="http://espn.go.com/nfl/team/_/name/ten/tennessee-titans">Tennessee</a></td>
<td>4:05 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/tennessee-titans-nashville-lp-field-29-9-2013-4271265?gcid=C12289x445&amp;keyword=NFL+Schedule+Tennessee+Titans+20130929">4,562 available from $21</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/10">LP Field</a></td>
</tr>
<tr class="evenrow team-28-21 team-28-7"><td><a href="http://espn.go.com/nfl/team/_/name/phi/philadelphia-eagles">Philadelphia</a> at <a href="http://espn.go.com/nfl/team/_/name/den/denver-broncos">Denver</a></td>
<td>4:25 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/denver-broncos-denver-sports-authority-field-at-mile-high-29-9-2013-4270599?gcid=C12289x445&amp;keyword=NFL+Schedule+Denver+Broncos+20130929">2,371 available from $68</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/7">Sports Authority Field at Mile High</a></td>
</tr>
<tr class="oddrow team-28-6 team-28-24"><td><a href="http://espn.go.com/nfl/team/_/name/dal/dallas-cowboys">Dallas</a> at <a href="http://espn.go.com/nfl/team/_/name/sd/san-diego-chargers">San Diego</a></td>
<td>4:25 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/san-diego-chargers-san-diego-qualcomm-stadium-29-9-2013-4270612?gcid=C12289x445&amp;keyword=NFL+Schedule+San+Diego+Chargers+20130929">9,126 available from $109</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/24">Qualcomm Stadium</a></td>
</tr>
<tr class="evenrow team-28-28 team-28-13"><td><a href="http://espn.go.com/nfl/team/_/name/wsh/washington-redskins">Washington</a> at <a href="http://espn.go.com/nfl/team/_/name/oak/oakland-raiders">Oakland</a></td>
<td>4:25 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/oakland-raiders-oakland-o-co-coliseum-29-9-2013-4271154?gcid=C12289x445&amp;keyword=NFL+Schedule+Oakland+Raiders+20130929">6,342 available from $41</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/13">O.co Coliseum</a></td>
</tr>
<tr class="oddrow team-28-17 team-28-1"><td><a href="http://espn.go.com/nfl/team/_/name/ne/new-england-patriots">New England</a> at <a href="http://espn.go.com/nfl/team/_/name/atl/atlanta-falcons">Atlanta</a></td>
<td>8:30 PM</td>
<td align="center">NBC</td><td><a href="http://www.stubhub.com/atlanta-falcons-atlanta-georgia-dome-29-9-2013-4270629?gcid=C12289x445&amp;keyword=NFL+Schedule+Atlanta+Falcons+20130929">5,388 available from $57</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/1">Georgia Dome</a></td>
</tr>
<tr class="colhead">
<td width="170">MON, SEP 30</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-15 team-28-18"><td><a href="http://espn.go.com/nfl/team/_/name/mia/miami-dolphins">Miami</a> at <a href="http://espn.go.com/nfl/team/_/name/no/new-orleans-saints">New Orleans</a></td>
<td>8:30 PM</td>
<td align="center"><a href="http://sports.espn.go.com/espntv/espnNetwork?networkID=1"><img src="http://a.espncdn.com/i/scoreboard/networkLogo_espn.gif" width="55" height="14"></a><a href="http://espn.go.com/watchespn/?channel=espn&amp;launchPlayer=true"><img src="http://a.espncdn.com/i/scoreboard/watchespn-55x12.png"></a></td><td><a href="http://www.stubhub.com/new-orleans-saints-new-orleans-mercedes-benz-superdome-30-9-2013-4271185?gcid=C12289x445&amp;keyword=NFL+Schedule+New+Orleans+Saints+20130930">3,371 available from $98</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/18">Mercedes-Benz Superdome</a></td>
</tr>
<tr class="evenrow"><td colspan="5">Bye: <a href="http://espn.go.com/nfl/team/_/name/gb/green-bay-packers">Green Bay</a>, <a href="http://espn.go.com/nfl/team/_/name/car/carolina-panthers">Carolina</a></td></tr></tbody></table><table class="tablehead" cellpadding="3" cellspacing="1"><tbody><tr class="stathead"><td colspan="5"><a name="5" style="color:#FFFFFF;"></a>Week 5<span><a href="#top">back to top �</a></span></td></tr>
<tr class="colhead">
<td width="170">THU, OCT 3</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-2 team-28-5"><td><a href="http://espn.go.com/nfl/team/_/name/buf/buffalo-bills">Buffalo</a> at <a href="http://espn.go.com/nfl/team/_/name/cle/cleveland-browns">Cleveland</a></td>
<td>8:25 PM</td>
<td align="center">NFL</td><td><a href="http://www.stubhub.com/cleveland-browns-cleveland-firstenergy-stadium-3-10-2013-4270660?gcid=C12289x445&amp;keyword=NFL+Schedule+Cleveland+Browns+20131003">5,110 available from $32</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/5">FirstEnergy Stadium</a></td>
</tr>
<tr class="colhead">
<td width="170">SUN, OCT 6</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-18 team-28-3"><td><a href="http://espn.go.com/nfl/team/_/name/no/new-orleans-saints">New Orleans</a> at <a href="http://espn.go.com/nfl/team/_/name/chi/chicago-bears">Chicago</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/chicago-bears-chicago-soldier-field-6-10-2013-4270601?gcid=C12289x445&amp;keyword=NFL+Schedule+Chicago+Bears+20131006">3,421 available from $110</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/3">Soldier Field</a></td>
</tr>
<tr class="evenrow team-28-17 team-28-4"><td><a href="http://espn.go.com/nfl/team/_/name/ne/new-england-patriots">New England</a> at <a href="http://espn.go.com/nfl/team/_/name/cin/cincinnati-bengals">Cincinnati</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/cincinnati-bengals-cincinnati-paul-brown-stadium-6-10-2013-4271211?gcid=C12289x445&amp;keyword=NFL+Schedule+Cincinnati+Bengals+20131006">10,405 available from $58</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/4">Paul Brown Stadium</a></td>
</tr>
<tr class="oddrow team-28-30 team-28-14"><td><a href="http://espn.go.com/nfl/team/_/name/jac/jacksonville-jaguars">Jacksonville</a> at <a href="http://espn.go.com/nfl/team/_/name/stl/st.-louis-rams">St. Louis</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/st-louis-rams-saint-louis-edward-jones-dome-6-10-2013-4270698?gcid=C12289x445&amp;keyword=NFL+Schedule+St.+Louis+Rams+20131006">7,433 available from $14</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/14">Edward Jones Dome</a></td>
</tr>
<tr class="evenrow team-28-33 team-28-15"><td><a href="http://espn.go.com/nfl/team/_/name/bal/baltimore-ravens">Baltimore</a> at <a href="http://espn.go.com/nfl/team/_/name/mia/miami-dolphins">Miami</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/miami-dolphins-miami-gardens-sun-life-stadium-6-10-2013-4270642?gcid=C12289x445&amp;keyword=NFL+Schedule+Miami+Dolphins+20131006">4,741 available from $32</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/15">Sun Life Stadium</a></td>
</tr>
<tr class="oddrow team-28-21 team-28-19"><td><a href="http://espn.go.com/nfl/team/_/name/phi/philadelphia-eagles">Philadelphia</a> at <a href="http://espn.go.com/nfl/team/_/name/nyg/new-york-giants">NY Giants</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/new-york-giants-east-rutherford-metlife-stadium-6-10-2013-4271174?gcid=C12289x445&amp;keyword=NFL+Schedule+New+York+Giants+20131006">5,197 available from $50</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/19">MetLife Stadium</a></td>
</tr>
<tr class="evenrow team-28-8 team-28-9"><td><a href="http://espn.go.com/nfl/team/_/name/det/detroit-lions">Detroit</a> at <a href="http://espn.go.com/nfl/team/_/name/gb/green-bay-packers">Green Bay</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/green-bay-packers-green-bay-lambeau-field-6-10-2013-4271137?gcid=C12289x445&amp;keyword=NFL+Schedule+Green+Bay+Packers+20131006">1,622 available from $93</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/9">Lambeau Field</a></td>
</tr>
<tr class="oddrow team-28-12 team-28-10"><td><a href="http://espn.go.com/nfl/team/_/name/kc/kansas-city-chiefs">Kansas City</a> at <a href="http://espn.go.com/nfl/team/_/name/ten/tennessee-titans">Tennessee</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/tennessee-titans-nashville-lp-field-6-10-2013-4270684?gcid=C12289x445&amp;keyword=NFL+Schedule+Tennessee+Titans+20131006">3,800 available from $25</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/10">LP Field</a></td>
</tr>
<tr class="evenrow team-28-26 team-28-11"><td><a href="http://espn.go.com/nfl/team/_/name/sea/seattle-seahawks">Seattle</a> at <a href="http://espn.go.com/nfl/team/_/name/ind/indianapolis-colts">Indianapolis</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/indianapolis-colts-indianapolis-lucas-oil-stadium-6-10-2013-4271253?gcid=C12289x445&amp;keyword=NFL+Schedule+Indianapolis+Colts+20131006">4,487 available from $26</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/11">Lucas Oil Stadium</a></td>
</tr>
<tr class="oddrow team-28-29 team-28-22"><td><a href="http://espn.go.com/nfl/team/_/name/car/carolina-panthers">Carolina</a> at <a href="http://espn.go.com/nfl/team/_/name/ari/arizona-cardinals">Arizona</a></td>
<td>4:05 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/arizona-cardinals-glendale-university-of-phoenix-stadium-6-10-2013-4271224?gcid=C12289x445&amp;keyword=NFL+Schedule+Arizona+Cardinals+20131006">8,336 available from $5</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/22">U of Phoenix Stadium</a></td>
</tr>
<tr class="evenrow team-28-7 team-28-6"><td><a href="http://espn.go.com/nfl/team/_/name/den/denver-broncos">Denver</a> at <a href="http://espn.go.com/nfl/team/_/name/dal/dallas-cowboys">Dallas</a></td>
<td>4:25 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/dallas-cowboys-arlington-cowboys-stadium-6-10-2013-4270643?gcid=C12289x445&amp;keyword=NFL+Schedule+Dallas+Cowboys+20131006">14,458 available from $46</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/6">AT&amp;T Stadium</a></td>
</tr>
<tr class="oddrow team-28-24 team-28-13"><td><a href="http://espn.go.com/nfl/team/_/name/sd/san-diego-chargers">San Diego</a> at <a href="http://espn.go.com/nfl/team/_/name/oak/oakland-raiders">Oakland</a></td>
<td>4:25 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/oakland-raiders-oakland-o-co-coliseum-6-10-2013-4270638?gcid=C12289x445&amp;keyword=NFL+Schedule+Oakland+Raiders+20131006">6,371 available from $28</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/13">O.co Coliseum</a></td>
</tr>
<tr class="evenrow team-28-34 team-28-25"><td><a href="http://espn.go.com/nfl/team/_/name/hou/houston-texans">Houston</a> at <a href="http://espn.go.com/nfl/team/_/name/sf/san-francisco-49ers">San Francisco</a></td>
<td>8:30 PM</td>
<td align="center">NBC</td><td><a href="http://www.stubhub.com/san-francisco-49ers-san-francisco-candlestick-park-6-10-2013-4271109?gcid=C12289x445&amp;keyword=NFL+Schedule+San+Francisco+49ers+20131006">12,377 available from $84</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/25">Candlestick Park</a></td>
</tr>
<tr class="colhead">
<td width="170">MON, OCT 7</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-20 team-28-1"><td><a href="http://espn.go.com/nfl/team/_/name/nyj/new-york-jets">NY Jets</a> at <a href="http://espn.go.com/nfl/team/_/name/atl/atlanta-falcons">Atlanta</a></td>
<td>8:30 PM</td>
<td align="center"><a href="http://sports.espn.go.com/espntv/espnNetwork?networkID=1"><img src="http://a.espncdn.com/i/scoreboard/networkLogo_espn.gif" width="55" height="14"></a><a href="http://espn.go.com/watchespn/?channel=espn&amp;launchPlayer=true"><img src="http://a.espncdn.com/i/scoreboard/watchespn-55x12.png"></a></td><td><a href="http://www.stubhub.com/atlanta-falcons-atlanta-georgia-dome-7-10-2013-4270630?gcid=C12289x445&amp;keyword=NFL+Schedule+Atlanta+Falcons+20131007">9,665 available from $37</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/1">Georgia Dome</a></td>
</tr>
<tr class="evenrow"><td colspan="5">Bye: <a href="http://espn.go.com/nfl/team/_/name/min/minnesota-vikings">Minnesota</a>, <a href="http://espn.go.com/nfl/team/_/name/pit/pittsburgh-steelers">Pittsburgh</a>, <a href="http://espn.go.com/nfl/team/_/name/tb/tampa-bay-buccaneers">Tampa Bay</a>, <a href="http://espn.go.com/nfl/team/_/name/wsh/washington-redskins">Washington</a></td></tr></tbody></table><table class="tablehead" cellpadding="3" cellspacing="1"><tbody><tr class="stathead"><td colspan="5"><a name="6" style="color:#FFFFFF;"></a>Week 6<span><a href="#top">back to top �</a></span></td></tr>
<tr class="colhead">
<td width="170">THU, OCT 10</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-19 team-28-3"><td><a href="http://espn.go.com/nfl/team/_/name/nyg/new-york-giants">NY Giants</a> at <a href="http://espn.go.com/nfl/team/_/name/chi/chicago-bears">Chicago</a></td>
<td>8:25 PM</td>
<td align="center">NFL</td><td><a href="http://www.stubhub.com/chicago-bears-chicago-soldier-field-10-10-2013-4270603?gcid=C12289x445&amp;keyword=NFL+Schedule+Chicago+Bears+20131010">5,167 available from $85</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/3">Soldier Field</a></td>
</tr>
<tr class="colhead">
<td width="170">SUN, OCT 13</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-4 team-28-2"><td><a href="http://espn.go.com/nfl/team/_/name/cin/cincinnati-bengals">Cincinnati</a> at <a href="http://espn.go.com/nfl/team/_/name/buf/buffalo-bills">Buffalo</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/buffalo-bills-orchard-park-ralph-wilson-stadium-13-10-2013-4270665?gcid=C12289x445&amp;keyword=NFL+Schedule+Buffalo+Bills+20131013">5,648 available from $26</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/2">Ralph Wilson Stadium</a></td>
</tr>
<tr class="evenrow team-28-8 team-28-5"><td><a href="http://espn.go.com/nfl/team/_/name/det/detroit-lions">Detroit</a> at <a href="http://espn.go.com/nfl/team/_/name/cle/cleveland-browns">Cleveland</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/cleveland-browns-cleveland-firstenergy-stadium-13-10-2013-4270664?gcid=C12289x445&amp;keyword=NFL+Schedule+Cleveland+Browns+20131013">4,450 available from $29</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/5">FirstEnergy Stadium</a></td>
</tr>
<tr class="oddrow team-28-13 team-28-12"><td><a href="http://espn.go.com/nfl/team/_/name/oak/oakland-raiders">Oakland</a> at <a href="http://espn.go.com/nfl/team/_/name/kc/kansas-city-chiefs">Kansas City</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/kansas-city-chiefs-kansas-city-arrowhead-stadium-13-10-2013-4271186?gcid=C12289x445&amp;keyword=NFL+Schedule+Kansas+City+Chiefs+20131013">9,254 available from $23</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/12">Arrowhead Stadium</a></td>
</tr>
<tr class="evenrow team-28-29 team-28-16"><td><a href="http://espn.go.com/nfl/team/_/name/car/carolina-panthers">Carolina</a> at <a href="http://espn.go.com/nfl/team/_/name/min/minnesota-vikings">Minnesota</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/minnesota-vikings-minneapolis-hubert-h--humphrey-metrodome-13-10-2013-4271152?gcid=C12289x445&amp;keyword=NFL+Schedule+Minnesota+Vikings+20131013">6,134 available from $14</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/16">Mall of America Field</a></td>
</tr>
<tr class="oddrow team-28-21 team-28-27"><td><a href="http://espn.go.com/nfl/team/_/name/phi/philadelphia-eagles">Philadelphia</a> at <a href="http://espn.go.com/nfl/team/_/name/tb/tampa-bay-buccaneers">Tampa Bay</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/tampa-bay-buccaneers-tampa-raymond-james-stadium-13-10-2013-4270687?gcid=C12289x445&amp;keyword=NFL+Schedule+Tampa+Bay+Buccaneers+20131013">4,690 available from $46</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/27">Raymond James Stadium</a></td>
</tr>
<tr class="evenrow team-28-9 team-28-33"><td><a href="http://espn.go.com/nfl/team/_/name/gb/green-bay-packers">Green Bay</a> at <a href="http://espn.go.com/nfl/team/_/name/bal/baltimore-ravens">Baltimore</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/baltimore-ravens-baltimore-m-t-bank-stadium-13-10-2013-4271189?gcid=C12289x445&amp;keyword=NFL+Schedule+Baltimore+Ravens+20131013">2,636 available from $140</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/33">M&amp;T Bank Stadium</a></td>
</tr>
<tr class="oddrow team-28-14 team-28-34"><td><a href="http://espn.go.com/nfl/team/_/name/stl/st.-louis-rams">St. Louis</a> at <a href="http://espn.go.com/nfl/team/_/name/hou/houston-texans">Houston</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/houston-texans-houston-reliant-stadium-13-10-2013-4271236?gcid=C12289x445&amp;keyword=NFL+Schedule+Houston+Texans+20131013">5,694 available from $39</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/34">Reliant Stadium</a></td>
</tr>
<tr class="evenrow team-28-23 team-28-20"><td><a href="http://espn.go.com/nfl/team/_/name/pit/pittsburgh-steelers">Pittsburgh</a> at <a href="http://espn.go.com/nfl/team/_/name/nyj/new-york-jets">NY Jets</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/new-york-jets-east-rutherford-metlife-stadium-13-10-2013-4271116?gcid=C12289x445&amp;keyword=NFL+Schedule+New+York+Jets+20131013">8,815 available from $35</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/20">MetLife Stadium</a></td>
</tr>
<tr class="oddrow team-28-30 team-28-7"><td><a href="http://espn.go.com/nfl/team/_/name/jac/jacksonville-jaguars">Jacksonville</a> at <a href="http://espn.go.com/nfl/team/_/name/den/denver-broncos">Denver</a></td>
<td>4:05 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/denver-broncos-denver-sports-authority-field-at-mile-high-13-10-2013-4270602?gcid=C12289x445&amp;keyword=NFL+Schedule+Denver+Broncos+20131013">2,674 available from $56</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/7">Sports Authority Field at Mile High</a></td>
</tr>
<tr class="evenrow team-28-10 team-28-26"><td><a href="http://espn.go.com/nfl/team/_/name/ten/tennessee-titans">Tennessee</a> at <a href="http://espn.go.com/nfl/team/_/name/sea/seattle-seahawks">Seattle</a></td>
<td>4:05 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/seattle-seahawks-seattle-centurylink-field-13-10-2013-4271130?gcid=C12289x445&amp;keyword=NFL+Schedule+Seattle+Seahawks+20131013">4,586 available from $87</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/26">CenturyLink Field</a></td>
</tr>
<tr class="oddrow team-28-18 team-28-17"><td><a href="http://espn.go.com/nfl/team/_/name/no/new-orleans-saints">New Orleans</a> at <a href="http://espn.go.com/nfl/team/_/name/ne/new-england-patriots">New England</a></td>
<td>4:25 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/new-england-patriots-foxborough-gillette-stadium-13-10-2013-4271144?gcid=C12289x445&amp;keyword=NFL+Schedule+New+England+Patriots+20131013">2,307 available from $149</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/17">Gillette Stadium</a></td>
</tr>
<tr class="evenrow team-28-22 team-28-25"><td><a href="http://espn.go.com/nfl/team/_/name/ari/arizona-cardinals">Arizona</a> at <a href="http://espn.go.com/nfl/team/_/name/sf/san-francisco-49ers">San Francisco</a></td>
<td>4:25 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/san-francisco-49ers-san-francisco-candlestick-park-13-10-2013-4271110?gcid=C12289x445&amp;keyword=NFL+Schedule+San+Francisco+49ers+20131013">11,969 available from $66</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/25">Candlestick Park</a></td>
</tr>
<tr class="oddrow team-28-28 team-28-6"><td><a href="http://espn.go.com/nfl/team/_/name/wsh/washington-redskins">Washington</a> at <a href="http://espn.go.com/nfl/team/_/name/dal/dallas-cowboys">Dallas</a></td>
<td>8:30 PM</td>
<td align="center">NBC</td><td><a href="http://www.stubhub.com/dallas-cowboys-arlington-cowboys-stadium-13-10-2013-4270645?gcid=C12289x445&amp;keyword=NFL+Schedule+Dallas+Cowboys+20131013">12,588 available from $36</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/6">AT&amp;T Stadium</a></td>
</tr>
<tr class="colhead">
<td width="170">MON, OCT 14</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-11 team-28-24"><td><a href="http://espn.go.com/nfl/team/_/name/ind/indianapolis-colts">Indianapolis</a> at <a href="http://espn.go.com/nfl/team/_/name/sd/san-diego-chargers">San Diego</a></td>
<td>8:30 PM</td>
<td align="center"><a href="http://sports.espn.go.com/espntv/espnNetwork?networkID=1"><img src="http://a.espncdn.com/i/scoreboard/networkLogo_espn.gif" width="55" height="14"></a><a href="http://espn.go.com/watchespn/?channel=espn&amp;launchPlayer=true"><img src="http://a.espncdn.com/i/scoreboard/watchespn-55x12.png"></a></td><td><a href="http://www.stubhub.com/san-diego-chargers-san-diego-qualcomm-stadium-14-10-2013-4270615?gcid=C12289x445&amp;keyword=NFL+Schedule+San+Diego+Chargers+20131014">9,967 available from $56</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/24">Qualcomm Stadium</a></td>
</tr>
<tr class="evenrow"><td colspan="5">Bye: <a href="http://espn.go.com/nfl/team/_/name/atl/atlanta-falcons">Atlanta</a>, <a href="http://espn.go.com/nfl/team/_/name/mia/miami-dolphins">Miami</a></td></tr></tbody></table><table class="tablehead" cellpadding="3" cellspacing="1"><tbody><tr class="stathead"><td colspan="5"><a name="7" style="color:#FFFFFF;"></a>Week 7<span><a href="#top">back to top �</a></span></td></tr>
<tr class="colhead">
<td width="170">THU, OCT 17</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-26 team-28-22"><td><a href="http://espn.go.com/nfl/team/_/name/sea/seattle-seahawks">Seattle</a> at <a href="http://espn.go.com/nfl/team/_/name/ari/arizona-cardinals">Arizona</a></td>
<td>8:25 PM</td>
<td align="center">NFL</td><td><a href="http://www.stubhub.com/arizona-cardinals-glendale-university-of-phoenix-stadium-17-10-2013-4270654?gcid=C12289x445&amp;keyword=NFL+Schedule+Arizona+Cardinals+20131017">6,289 available from $6</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/22">U of Phoenix Stadium</a></td>
</tr>
<tr class="colhead">
<td width="170">SUN, OCT 20</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-27 team-28-1"><td><a href="http://espn.go.com/nfl/team/_/name/tb/tampa-bay-buccaneers">Tampa Bay</a> at <a href="http://espn.go.com/nfl/team/_/name/atl/atlanta-falcons">Atlanta</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/atlanta-falcons-atlanta-georgia-dome-20-10-2013-4270634?gcid=C12289x445&amp;keyword=NFL+Schedule+Atlanta+Falcons+20131020">8,363 available from $30</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/1">Georgia Dome</a></td>
</tr>
<tr class="evenrow team-28-4 team-28-8"><td><a href="http://espn.go.com/nfl/team/_/name/cin/cincinnati-bengals">Cincinnati</a> at <a href="http://espn.go.com/nfl/team/_/name/det/detroit-lions">Detroit</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/detroit-lions-detroit-ford-field-20-10-2013-4271132?gcid=C12289x445&amp;keyword=NFL+Schedule+Detroit+Lions+20131020">12,691 available from $32</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/8">Ford Field</a></td>
</tr>
<tr class="oddrow team-28-34 team-28-12"><td><a href="http://espn.go.com/nfl/team/_/name/hou/houston-texans">Houston</a> at <a href="http://espn.go.com/nfl/team/_/name/kc/kansas-city-chiefs">Kansas City</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/kansas-city-chiefs-kansas-city-arrowhead-stadium-20-10-2013-4271188?gcid=C12289x445&amp;keyword=NFL+Schedule+Kansas+City+Chiefs+20131020">10,150 available from $24</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/12">Arrowhead Stadium</a></td>
</tr>
<tr class="evenrow team-28-2 team-28-15"><td><a href="http://espn.go.com/nfl/team/_/name/buf/buffalo-bills">Buffalo</a> at <a href="http://espn.go.com/nfl/team/_/name/mia/miami-dolphins">Miami</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/miami-dolphins-miami-gardens-sun-life-stadium-20-10-2013-4271162?gcid=C12289x445&amp;keyword=NFL+Schedule+Miami+Dolphins+20131020">5,740 available from $30</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/15">Sun Life Stadium</a></td>
</tr>
<tr class="oddrow team-28-17 team-28-20"><td><a href="http://espn.go.com/nfl/team/_/name/ne/new-england-patriots">New England</a> at <a href="http://espn.go.com/nfl/team/_/name/nyj/new-york-jets">NY Jets</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/new-york-jets-east-rutherford-metlife-stadium-20-10-2013-4271118?gcid=C12289x445&amp;keyword=NFL+Schedule+New+York+Jets+20131020">8,625 available from $35</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/20">MetLife Stadium</a></td>
</tr>
<tr class="evenrow team-28-6 team-28-21"><td><a href="http://espn.go.com/nfl/team/_/name/dal/dallas-cowboys">Dallas</a> at <a href="http://espn.go.com/nfl/team/_/name/phi/philadelphia-eagles">Philadelphia</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/philadelphia-eagles-philadelphia-lincoln-financial-field-20-10-2013-4270650?gcid=C12289x445&amp;keyword=NFL+Schedule+Philadelphia+Eagles+20131020">5,024 available from $74</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/21">Lincoln Financial Field</a></td>
</tr>
<tr class="oddrow team-28-3 team-28-28"><td><a href="http://espn.go.com/nfl/team/_/name/chi/chicago-bears">Chicago</a> at <a href="http://espn.go.com/nfl/team/_/name/wsh/washington-redskins">Washington</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/washington-redskins-landover-fedexfield-20-10-2013-4271216?gcid=C12289x445&amp;keyword=NFL+Schedule+Washington+Redskins+20131020">7,723 available from $37</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/28">FedEx Field</a></td>
</tr>
<tr class="evenrow team-28-14 team-28-29"><td><a href="http://espn.go.com/nfl/team/_/name/stl/st.-louis-rams">St. Louis</a> at <a href="http://espn.go.com/nfl/team/_/name/car/carolina-panthers">Carolina</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/carolina-panthers-charlotte-bank-of-america-stadium-20-10-2013-4271280?gcid=C12289x445&amp;keyword=NFL+Schedule+Carolina+Panthers+20131020">5,446 available from $31</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/29">Bank of America Stadium</a></td>
</tr>
<tr class="oddrow team-28-24 team-28-30"><td><a href="http://espn.go.com/nfl/team/_/name/sd/san-diego-chargers">San Diego</a> at <a href="http://espn.go.com/nfl/team/_/name/jac/jacksonville-jaguars">Jacksonville</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/jacksonville-jaguars-jacksonville-everbank-field-20-10-2013-4270679?gcid=C12289x445&amp;keyword=NFL+Schedule+Jacksonville+Jaguars+20131020">3,351 available from $38</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/30">EverBank Field</a></td>
</tr>
<tr class="evenrow team-28-25 team-28-10"><td><a href="http://espn.go.com/nfl/team/_/name/sf/san-francisco-49ers">San Francisco</a> at <a href="http://espn.go.com/nfl/team/_/name/ten/tennessee-titans">Tennessee</a></td>
<td>4:05 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/tennessee-titans-nashville-lp-field-20-10-2013-4270685?gcid=C12289x445&amp;keyword=NFL+Schedule+Tennessee+Titans+20131020">4,337 available from $63</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/10">LP Field</a></td>
</tr>
<tr class="oddrow team-28-33 team-28-23"><td><a href="http://espn.go.com/nfl/team/_/name/bal/baltimore-ravens">Baltimore</a> at <a href="http://espn.go.com/nfl/team/_/name/pit/pittsburgh-steelers">Pittsburgh</a></td>
<td>4:25 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/pittsburgh-steelers-pittsburgh-heinz-field-20-10-2013-4270673?gcid=C12289x445&amp;keyword=NFL+Schedule+Pittsburgh+Steelers+20131020">3,830 available from $60</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/23">Heinz Field</a></td>
</tr>
<tr class="evenrow team-28-5 team-28-9"><td><a href="http://espn.go.com/nfl/team/_/name/cle/cleveland-browns">Cleveland</a> at <a href="http://espn.go.com/nfl/team/_/name/gb/green-bay-packers">Green Bay</a></td>
<td>4:25 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/green-bay-packers-green-bay-lambeau-field-20-10-2013-4270619?gcid=C12289x445&amp;keyword=NFL+Schedule+Green+Bay+Packers+20131020">3,655 available from $81</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/9">Lambeau Field</a></td>
</tr>
<tr class="oddrow team-28-7 team-28-11"><td><a href="http://espn.go.com/nfl/team/_/name/den/denver-broncos">Denver</a> at <a href="http://espn.go.com/nfl/team/_/name/ind/indianapolis-colts">Indianapolis</a></td>
<td>8:30 PM</td>
<td align="center">NBC</td><td><a href="http://www.stubhub.com/indianapolis-colts-indianapolis-lucas-oil-stadium-20-10-2013-4270680?gcid=C12289x445&amp;keyword=NFL+Schedule+Indianapolis+Colts+20131020">6,037 available from $189</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/11">Lucas Oil Stadium</a></td>
</tr>
<tr class="colhead">
<td width="170">MON, OCT 21</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-16 team-28-19"><td><a href="http://espn.go.com/nfl/team/_/name/min/minnesota-vikings">Minnesota</a> at <a href="http://espn.go.com/nfl/team/_/name/nyg/new-york-giants">NY Giants</a></td>
<td>8:30 PM</td>
<td align="center"><a href="http://sports.espn.go.com/espntv/espnNetwork?networkID=1"><img src="http://a.espncdn.com/i/scoreboard/networkLogo_espn.gif" width="55" height="14"></a><a href="http://espn.go.com/watchespn/?channel=espn&amp;launchPlayer=true"><img src="http://a.espncdn.com/i/scoreboard/watchespn-55x12.png"></a></td><td><a href="http://www.stubhub.com/new-york-giants-east-rutherford-metlife-stadium-21-10-2013-4271175?gcid=C12289x445&amp;keyword=NFL+Schedule+New+York+Giants+20131021">6,588 available from $40</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/19">MetLife Stadium</a></td>
</tr>
<tr class="evenrow"><td colspan="5">Bye: <a href="http://espn.go.com/nfl/team/_/name/oak/oakland-raiders">Oakland</a>, <a href="http://espn.go.com/nfl/team/_/name/no/new-orleans-saints">New Orleans</a></td></tr></tbody></table><table class="tablehead" cellpadding="3" cellspacing="1"><tbody><tr class="stathead"><td colspan="5"><a name="8" style="color:#FFFFFF;"></a>Week 8<span><a href="#top">back to top �</a></span></td></tr>
<tr class="colhead">
<td width="170">THU, OCT 24</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-29 team-28-27"><td><a href="http://espn.go.com/nfl/team/_/name/car/carolina-panthers">Carolina</a> at <a href="http://espn.go.com/nfl/team/_/name/tb/tampa-bay-buccaneers">Tampa Bay</a></td>
<td>8:25 PM</td>
<td align="center">NFL</td><td><a href="http://www.stubhub.com/tampa-bay-buccaneers-tampa-raymond-james-stadium-24-10-2013-4271268?gcid=C12289x445&amp;keyword=NFL+Schedule+Tampa+Bay+Buccaneers+20131024">5,414 available from $32</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/27">Raymond James Stadium</a></td>
</tr>
<tr class="colhead">
<td width="170">SUN, OCT 27</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-6 team-28-8"><td><a href="http://espn.go.com/nfl/team/_/name/dal/dallas-cowboys">Dallas</a> at <a href="http://espn.go.com/nfl/team/_/name/det/detroit-lions">Detroit</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/detroit-lions-detroit-ford-field-27-10-2013-4270608?gcid=C12289x445&amp;keyword=NFL+Schedule+Detroit+Lions+20131027">12,467 available from $74</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/8">Ford Field</a></td>
</tr>
<tr class="evenrow team-28-5 team-28-12"><td><a href="http://espn.go.com/nfl/team/_/name/cle/cleveland-browns">Cleveland</a> at <a href="http://espn.go.com/nfl/team/_/name/kc/kansas-city-chiefs">Kansas City</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/kansas-city-chiefs-kansas-city-arrowhead-stadium-27-10-2013-4271191?gcid=C12289x445&amp;keyword=NFL+Schedule+Kansas+City+Chiefs+20131027">9,877 available from $16</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/12">Arrowhead Stadium</a></td>
</tr>
<tr class="oddrow team-28-15 team-28-17"><td><a href="http://espn.go.com/nfl/team/_/name/mia/miami-dolphins">Miami</a> at <a href="http://espn.go.com/nfl/team/_/name/ne/new-england-patriots">New England</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/new-england-patriots-foxborough-gillette-stadium-27-10-2013-4271145?gcid=C12289x445&amp;keyword=NFL+Schedule+New+England+Patriots+20131027">2,367 available from $137</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/17">Gillette Stadium</a></td>
</tr>
<tr class="evenrow team-28-2 team-28-18"><td><a href="http://espn.go.com/nfl/team/_/name/buf/buffalo-bills">Buffalo</a> at <a href="http://espn.go.com/nfl/team/_/name/no/new-orleans-saints">New Orleans</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/new-orleans-saints-new-orleans-mercedes-benz-superdome-27-10-2013-4271192?gcid=C12289x445&amp;keyword=NFL+Schedule+New+Orleans+Saints+20131027">2,898 available from $74</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/18">Mercedes-Benz Superdome</a></td>
</tr>
<tr class="oddrow team-28-19 team-28-21"><td><a href="http://espn.go.com/nfl/team/_/name/nyg/new-york-giants">NY Giants</a> at <a href="http://espn.go.com/nfl/team/_/name/phi/philadelphia-eagles">Philadelphia</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/philadelphia-eagles-philadelphia-lincoln-financial-field-27-10-2013-4271202?gcid=C12289x445&amp;keyword=NFL+Schedule+Philadelphia+Eagles+20131027">5,542 available from $67</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/21">Lincoln Financial Field</a></td>
</tr>
<tr class="evenrow team-28-25 team-28-30"><td><a href="http://espn.go.com/nfl/team/_/name/sf/san-francisco-49ers">San Francisco</a> at <a href="http://espn.go.com/nfl/team/_/name/jac/jacksonville-jaguars">Jacksonville</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/jacksonville-london-wembley-stadium-27-10-2013-4179161?gcid=C12289x445&amp;keyword=NFL+Schedule+Jacksonville+Jaguars+20131027">657 available from $129</a></td>
<td>Wembley Stadium</td>
</tr>
<tr class="oddrow team-28-23 team-28-13"><td><a href="http://espn.go.com/nfl/team/_/name/pit/pittsburgh-steelers">Pittsburgh</a> at <a href="http://espn.go.com/nfl/team/_/name/oak/oakland-raiders">Oakland</a></td>
<td>4:05 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/oakland-raiders-oakland-o-co-coliseum-27-10-2013-4270639?gcid=C12289x445&amp;keyword=NFL+Schedule+Oakland+Raiders+20131027">7,762 available from $52</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/13">O.co Coliseum</a></td>
</tr>
<tr class="evenrow team-28-20 team-28-4"><td><a href="http://espn.go.com/nfl/team/_/name/nyj/new-york-jets">NY Jets</a> at <a href="http://espn.go.com/nfl/team/_/name/cin/cincinnati-bengals">Cincinnati</a></td>
<td>4:05 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/cincinnati-bengals-cincinnati-paul-brown-stadium-27-10-2013-4271213?gcid=C12289x445&amp;keyword=NFL+Schedule+Cincinnati+Bengals+20131027">9,526 available from $27</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/4">Paul Brown Stadium</a></td>
</tr>
<tr class="oddrow team-28-28 team-28-7"><td><a href="http://espn.go.com/nfl/team/_/name/wsh/washington-redskins">Washington</a> at <a href="http://espn.go.com/nfl/team/_/name/den/denver-broncos">Denver</a></td>
<td>4:25 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/denver-broncos-denver-sports-authority-field-at-mile-high-27-10-2013-4271117?gcid=C12289x445&amp;keyword=NFL+Schedule+Denver+Broncos+20131027">2,101 available from $68</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/7">Sports Authority Field at Mile High</a></td>
</tr>
<tr class="evenrow team-28-1 team-28-22"><td><a href="http://espn.go.com/nfl/team/_/name/atl/atlanta-falcons">Atlanta</a> at <a href="http://espn.go.com/nfl/team/_/name/ari/arizona-cardinals">Arizona</a></td>
<td>4:25 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/arizona-cardinals-glendale-university-of-phoenix-stadium-27-10-2013-4270655?gcid=C12289x445&amp;keyword=NFL+Schedule+Arizona+Cardinals+20131027">8,211 available from $5</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/22">U of Phoenix Stadium</a></td>
</tr>
<tr class="oddrow team-28-9 team-28-16"><td><a href="http://espn.go.com/nfl/team/_/name/gb/green-bay-packers">Green Bay</a> at <a href="http://espn.go.com/nfl/team/_/name/min/minnesota-vikings">Minnesota</a></td>
<td>8:30 PM</td>
<td align="center">NBC</td><td><a href="http://www.stubhub.com/minnesota-vikings-minneapolis-hubert-h--humphrey-metrodome-27-10-2013-4270632?gcid=C12289x445&amp;keyword=NFL+Schedule+Minnesota+Vikings+20131027">6,679 available from $111</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/16">Mall of America Field</a></td>
</tr>
<tr class="colhead">
<td width="170">MON, OCT 28</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-26 team-28-14"><td><a href="http://espn.go.com/nfl/team/_/name/sea/seattle-seahawks">Seattle</a> at <a href="http://espn.go.com/nfl/team/_/name/stl/st.-louis-rams">St. Louis</a></td>
<td>8:30 PM</td>
<td align="center"><a href="http://sports.espn.go.com/espntv/espnNetwork?networkID=1"><img src="http://a.espncdn.com/i/scoreboard/networkLogo_espn.gif" width="55" height="14"></a><a href="http://espn.go.com/watchespn/?channel=espn&amp;launchPlayer=true"><img src="http://a.espncdn.com/i/scoreboard/watchespn-55x12.png"></a></td><td><a href="http://www.stubhub.com/st-louis-rams-saint-louis-edward-jones-dome-28-10-2013-4271275?gcid=C12289x445&amp;keyword=NFL+Schedule+St.+Louis+Rams+20131028">9,307 available from $38</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/14">Edward Jones Dome</a></td>
</tr>
<tr class="evenrow"><td colspan="5">Bye: <a href="http://espn.go.com/nfl/team/_/name/chi/chicago-bears">Chicago</a>, <a href="http://espn.go.com/nfl/team/_/name/ten/tennessee-titans">Tennessee</a>, <a href="http://espn.go.com/nfl/team/_/name/ind/indianapolis-colts">Indianapolis</a>, <a href="http://espn.go.com/nfl/team/_/name/sd/san-diego-chargers">San Diego</a>, <a href="http://espn.go.com/nfl/team/_/name/bal/baltimore-ravens">Baltimore</a>, <a href="http://espn.go.com/nfl/team/_/name/hou/houston-texans">Houston</a></td></tr></tbody></table><table class="tablehead" cellpadding="3" cellspacing="1"><tbody><tr class="stathead"><td colspan="5"><a name="9" style="color:#FFFFFF;"></a>Week 9<span><a href="#top">back to top �</a></span></td></tr>
<tr class="colhead">
<td width="170">THU, OCT 31</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-4 team-28-15"><td><a href="http://espn.go.com/nfl/team/_/name/cin/cincinnati-bengals">Cincinnati</a> at <a href="http://espn.go.com/nfl/team/_/name/mia/miami-dolphins">Miami</a></td>
<td>8:25 PM</td>
<td align="center">NFL</td><td><a href="http://www.stubhub.com/miami-dolphins-miami-gardens-sun-life-stadium-31-10-2013-4270646?gcid=C12289x445&amp;keyword=NFL+Schedule+Miami+Dolphins+20131031">5,607 available from $27</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/15">Sun Life Stadium</a></td>
</tr>
<tr class="colhead">
<td width="170">SUN, NOV 3</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-12 team-28-2"><td><a href="http://espn.go.com/nfl/team/_/name/kc/kansas-city-chiefs">Kansas City</a> at <a href="http://espn.go.com/nfl/team/_/name/buf/buffalo-bills">Buffalo</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/buffalo-bills-orchard-park-ralph-wilson-stadium-3-11-2013-4270666?gcid=C12289x445&amp;keyword=NFL+Schedule+Buffalo+Bills+20131103">7,253 available from $26</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/2">Ralph Wilson Stadium</a></td>
</tr>
<tr class="evenrow team-28-24 team-28-28"><td><a href="http://espn.go.com/nfl/team/_/name/sd/san-diego-chargers">San Diego</a> at <a href="http://espn.go.com/nfl/team/_/name/wsh/washington-redskins">Washington</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/washington-redskins-landover-fedexfield-3-11-2013-4271218?gcid=C12289x445&amp;keyword=NFL+Schedule+Washington+Redskins+20131103">10,437 available from $33</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/28">FedEx Field</a></td>
</tr>
<tr class="oddrow team-28-1 team-28-29"><td><a href="http://espn.go.com/nfl/team/_/name/atl/atlanta-falcons">Atlanta</a> at <a href="http://espn.go.com/nfl/team/_/name/car/carolina-panthers">Carolina</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/carolina-panthers-charlotte-bank-of-america-stadium-3-11-2013-4270706?gcid=C12289x445&amp;keyword=NFL+Schedule+Carolina+Panthers+20131103">5,304 available from $36</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/29">Bank of America Stadium</a></td>
</tr>
<tr class="evenrow team-28-16 team-28-6"><td><a href="http://espn.go.com/nfl/team/_/name/min/minnesota-vikings">Minnesota</a> at <a href="http://espn.go.com/nfl/team/_/name/dal/dallas-cowboys">Dallas</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/dallas-cowboys-arlington-cowboys-stadium-3-11-2013-4271163?gcid=C12289x445&amp;keyword=NFL+Schedule+Dallas+Cowboys+20131103">12,124 available from $23</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/6">AT&amp;T Stadium</a></td>
</tr>
<tr class="oddrow team-28-10 team-28-14"><td><a href="http://espn.go.com/nfl/team/_/name/ten/tennessee-titans">Tennessee</a> at <a href="http://espn.go.com/nfl/team/_/name/stl/st.-louis-rams">St. Louis</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/st-louis-rams-saint-louis-edward-jones-dome-3-11-2013-4271276?gcid=C12289x445&amp;keyword=NFL+Schedule+St.+Louis+Rams+20131103">7,531 available from $18</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/14">Edward Jones Dome</a></td>
</tr>
<tr class="evenrow team-28-18 team-28-20"><td><a href="http://espn.go.com/nfl/team/_/name/no/new-orleans-saints">New Orleans</a> at <a href="http://espn.go.com/nfl/team/_/name/nyj/new-york-jets">NY Jets</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/new-york-jets-east-rutherford-metlife-stadium-3-11-2013-4271122?gcid=C12289x445&amp;keyword=NFL+Schedule+New+York+Jets+20131103">9,173 available from $31</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/20">MetLife Stadium</a></td>
</tr>
<tr class="oddrow team-28-27 team-28-26"><td><a href="http://espn.go.com/nfl/team/_/name/tb/tampa-bay-buccaneers">Tampa Bay</a> at <a href="http://espn.go.com/nfl/team/_/name/sea/seattle-seahawks">Seattle</a></td>
<td>4:05 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/seattle-seahawks-seattle-centurylink-field-3-11-2013-4270607?gcid=C12289x445&amp;keyword=NFL+Schedule+Seattle+Seahawks+20131103">5,771 available from $87</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/26">CenturyLink Field</a></td>
</tr>
<tr class="evenrow team-28-21 team-28-13"><td><a href="http://espn.go.com/nfl/team/_/name/phi/philadelphia-eagles">Philadelphia</a> at <a href="http://espn.go.com/nfl/team/_/name/oak/oakland-raiders">Oakland</a></td>
<td>4:05 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/oakland-raiders-oakland-o-co-coliseum-3-11-2013-4270640?gcid=C12289x445&amp;keyword=NFL+Schedule+Oakland+Raiders+20131103">6,779 available from $37</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/13">O.co Coliseum</a></td>
</tr>
<tr class="oddrow team-28-23 team-28-17"><td><a href="http://espn.go.com/nfl/team/_/name/pit/pittsburgh-steelers">Pittsburgh</a> at <a href="http://espn.go.com/nfl/team/_/name/ne/new-england-patriots">New England</a></td>
<td>4:25 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/new-england-patriots-foxborough-gillette-stadium-3-11-2013-4270628?gcid=C12289x445&amp;keyword=NFL+Schedule+New+England+Patriots+20131103">2,946 available from $155</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/17">Gillette Stadium</a></td>
</tr>
<tr class="evenrow team-28-33 team-28-5"><td><a href="http://espn.go.com/nfl/team/_/name/bal/baltimore-ravens">Baltimore</a> at <a href="http://espn.go.com/nfl/team/_/name/cle/cleveland-browns">Cleveland</a></td>
<td>4:25 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/cleveland-browns-cleveland-firstenergy-stadium-3-11-2013-4270667?gcid=C12289x445&amp;keyword=NFL+Schedule+Cleveland+Browns+20131103">7,077 available from $27</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/5">FirstEnergy Stadium</a></td>
</tr>
<tr class="oddrow team-28-11 team-28-34"><td><a href="http://espn.go.com/nfl/team/_/name/ind/indianapolis-colts">Indianapolis</a> at <a href="http://espn.go.com/nfl/team/_/name/hou/houston-texans">Houston</a></td>
<td>8:30 PM</td>
<td align="center">NBC</td><td><a href="http://www.stubhub.com/houston-texans-houston-reliant-stadium-3-11-2013-4270668?gcid=C12289x445&amp;keyword=NFL+Schedule+Houston+Texans+20131103">5,481 available from $38</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/34">Reliant Stadium</a></td>
</tr>
<tr class="colhead">
<td width="170">MON, NOV 4</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-3 team-28-9"><td><a href="http://espn.go.com/nfl/team/_/name/chi/chicago-bears">Chicago</a> at <a href="http://espn.go.com/nfl/team/_/name/gb/green-bay-packers">Green Bay</a></td>
<td>8:30 PM</td>
<td align="center"><a href="http://sports.espn.go.com/espntv/espnNetwork?networkID=1"><img src="http://a.espncdn.com/i/scoreboard/networkLogo_espn.gif" width="55" height="14"></a><a href="http://espn.go.com/watchespn/?channel=espn&amp;launchPlayer=true"><img src="http://a.espncdn.com/i/scoreboard/watchespn-55x12.png"></a></td><td><a href="http://www.stubhub.com/green-bay-packers-green-bay-lambeau-field-4-11-2013-4270622?gcid=C12289x445&amp;keyword=NFL+Schedule+Green+Bay+Packers+20131104">3,695 available from $93</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/9">Lambeau Field</a></td>
</tr>
<tr class="evenrow"><td colspan="5">Bye: <a href="http://espn.go.com/nfl/team/_/name/den/denver-broncos">Denver</a>, <a href="http://espn.go.com/nfl/team/_/name/det/detroit-lions">Detroit</a>, <a href="http://espn.go.com/nfl/team/_/name/nyg/new-york-giants">NY Giants</a>, <a href="http://espn.go.com/nfl/team/_/name/ari/arizona-cardinals">Arizona</a>, <a href="http://espn.go.com/nfl/team/_/name/sf/san-francisco-49ers">San Francisco</a>, <a href="http://espn.go.com/nfl/team/_/name/jac/jacksonville-jaguars">Jacksonville</a></td></tr></tbody></table><table class="tablehead" cellpadding="3" cellspacing="1"><tbody><tr class="stathead"><td colspan="5"><a name="10" style="color:#FFFFFF;"></a>Week 10<span><a href="#top">back to top �</a></span></td></tr>
<tr class="colhead">
<td width="170">THU, NOV 7</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-28 team-28-16"><td><a href="http://espn.go.com/nfl/team/_/name/wsh/washington-redskins">Washington</a> at <a href="http://espn.go.com/nfl/team/_/name/min/minnesota-vikings">Minnesota</a></td>
<td>8:25 PM</td>
<td align="center">NFL</td><td><a href="http://www.stubhub.com/minnesota-vikings-minneapolis-hubert-h--humphrey-metrodome-7-11-2013-4271153?gcid=C12289x445&amp;keyword=NFL+Schedule+Minnesota+Vikings+20131107">8,398 available from $39</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/16">Mall of America Field</a></td>
</tr>
<tr class="colhead">
<td width="170">SUN, NOV 10</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-26 team-28-1"><td><a href="http://espn.go.com/nfl/team/_/name/sea/seattle-seahawks">Seattle</a> at <a href="http://espn.go.com/nfl/team/_/name/atl/atlanta-falcons">Atlanta</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/atlanta-falcons-atlanta-georgia-dome-10-11-2013-4270636?gcid=C12289x445&amp;keyword=NFL+Schedule+Atlanta+Falcons+20131110">7,625 available from $30</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/1">Georgia Dome</a></td>
</tr>
<tr class="evenrow team-28-8 team-28-3"><td><a href="http://espn.go.com/nfl/team/_/name/det/detroit-lions">Detroit</a> at <a href="http://espn.go.com/nfl/team/_/name/chi/chicago-bears">Chicago</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/chicago-bears-chicago-soldier-field-10-11-2013-4271115?gcid=C12289x445&amp;keyword=NFL+Schedule+Chicago+Bears+20131110">5,366 available from $111</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/3">Soldier Field</a></td>
</tr>
<tr class="oddrow team-28-21 team-28-9"><td><a href="http://espn.go.com/nfl/team/_/name/phi/philadelphia-eagles">Philadelphia</a> at <a href="http://espn.go.com/nfl/team/_/name/gb/green-bay-packers">Green Bay</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/green-bay-packers-green-bay-lambeau-field-10-11-2013-4270624?gcid=C12289x445&amp;keyword=NFL+Schedule+Green+Bay+Packers+20131110">3,286 available from $93</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/9">Lambeau Field</a></td>
</tr>
<tr class="evenrow team-28-30 team-28-10"><td><a href="http://espn.go.com/nfl/team/_/name/jac/jacksonville-jaguars">Jacksonville</a> at <a href="http://espn.go.com/nfl/team/_/name/ten/tennessee-titans">Tennessee</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/tennessee-titans-nashville-lp-field-10-11-2013-4271267?gcid=C12289x445&amp;keyword=NFL+Schedule+Tennessee+Titans+20131110">4,851 available from $16</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/10">LP Field</a></td>
</tr>
<tr class="oddrow team-28-14 team-28-11"><td><a href="http://espn.go.com/nfl/team/_/name/stl/st.-louis-rams">St. Louis</a> at <a href="http://espn.go.com/nfl/team/_/name/ind/indianapolis-colts">Indianapolis</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/indianapolis-colts-indianapolis-lucas-oil-stadium-10-11-2013-4271256?gcid=C12289x445&amp;keyword=NFL+Schedule+Indianapolis+Colts+20131110">4,783 available from $26</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/11">Lucas Oil Stadium</a></td>
</tr>
<tr class="evenrow team-28-13 team-28-19"><td><a href="http://espn.go.com/nfl/team/_/name/oak/oakland-raiders">Oakland</a> at <a href="http://espn.go.com/nfl/team/_/name/nyg/new-york-giants">NY Giants</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/new-york-giants-east-rutherford-metlife-stadium-10-11-2013-4271176?gcid=C12289x445&amp;keyword=NFL+Schedule+New+York+Giants+20131110">5,420 available from $44</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/19">MetLife Stadium</a></td>
</tr>
<tr class="oddrow team-28-2 team-28-23"><td><a href="http://espn.go.com/nfl/team/_/name/buf/buffalo-bills">Buffalo</a> at <a href="http://espn.go.com/nfl/team/_/name/pit/pittsburgh-steelers">Pittsburgh</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/pittsburgh-steelers-pittsburgh-heinz-field-10-11-2013-4270674?gcid=C12289x445&amp;keyword=NFL+Schedule+Pittsburgh+Steelers+20131110">4,303 available from $66</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/23">Heinz Field</a></td>
</tr>
<tr class="evenrow team-28-4 team-28-33"><td><a href="http://espn.go.com/nfl/team/_/name/cin/cincinnati-bengals">Cincinnati</a> at <a href="http://espn.go.com/nfl/team/_/name/bal/baltimore-ravens">Baltimore</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/baltimore-ravens-baltimore-m-t-bank-stadium-10-11-2013-4270648?gcid=C12289x445&amp;keyword=NFL+Schedule+Baltimore+Ravens+20131110">3,460 available from $107</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/33">M&amp;T Bank Stadium</a></td>
</tr>
<tr class="oddrow team-28-29 team-28-25"><td><a href="http://espn.go.com/nfl/team/_/name/car/carolina-panthers">Carolina</a> at <a href="http://espn.go.com/nfl/team/_/name/sf/san-francisco-49ers">San Francisco</a></td>
<td>4:05 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/san-francisco-49ers-san-francisco-candlestick-park-10-11-2013-4270596?gcid=C12289x445&amp;keyword=NFL+Schedule+San+Francisco+49ers+20131110">12,302 available from $67</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/25">Candlestick Park</a></td>
</tr>
<tr class="evenrow team-28-7 team-28-24"><td><a href="http://espn.go.com/nfl/team/_/name/den/denver-broncos">Denver</a> at <a href="http://espn.go.com/nfl/team/_/name/sd/san-diego-chargers">San Diego</a></td>
<td>4:25 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/san-diego-chargers-san-diego-qualcomm-stadium-10-11-2013-4270618?gcid=C12289x445&amp;keyword=NFL+Schedule+San+Diego+Chargers+20131110">7,608 available from $74</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/24">Qualcomm Stadium</a></td>
</tr>
<tr class="oddrow team-28-34 team-28-22"><td><a href="http://espn.go.com/nfl/team/_/name/hou/houston-texans">Houston</a> at <a href="http://espn.go.com/nfl/team/_/name/ari/arizona-cardinals">Arizona</a></td>
<td>4:25 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/arizona-cardinals-glendale-university-of-phoenix-stadium-10-11-2013-4271231?gcid=C12289x445&amp;keyword=NFL+Schedule+Arizona+Cardinals+20131110">8,200 available from $10</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/22">U of Phoenix Stadium</a></td>
</tr>
<tr class="evenrow team-28-6 team-28-18"><td><a href="http://espn.go.com/nfl/team/_/name/dal/dallas-cowboys">Dallas</a> at <a href="http://espn.go.com/nfl/team/_/name/no/new-orleans-saints">New Orleans</a></td>
<td>8:30 PM</td>
<td align="center">NBC</td><td><a href="http://www.stubhub.com/new-orleans-saints-new-orleans-mercedes-benz-superdome-10-11-2013-4271196?gcid=C12289x445&amp;keyword=NFL+Schedule+New+Orleans+Saints+20131110">3,105 available from $173</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/18">Mercedes-Benz Superdome</a></td>
</tr>
<tr class="colhead">
<td width="170">MON, NOV 11</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-15 team-28-27"><td><a href="http://espn.go.com/nfl/team/_/name/mia/miami-dolphins">Miami</a> at <a href="http://espn.go.com/nfl/team/_/name/tb/tampa-bay-buccaneers">Tampa Bay</a></td>
<td>8:30 PM</td>
<td align="center"><a href="http://sports.espn.go.com/espntv/espnNetwork?networkID=1"><img src="http://a.espncdn.com/i/scoreboard/networkLogo_espn.gif" width="55" height="14"></a><a href="http://espn.go.com/watchespn/?channel=espn&amp;launchPlayer=true"><img src="http://a.espncdn.com/i/scoreboard/watchespn-55x12.png"></a></td><td><a href="http://www.stubhub.com/tampa-bay-buccaneers-tampa-raymond-james-stadium-11-11-2013-4271270?gcid=C12289x445&amp;keyword=NFL+Schedule+Tampa+Bay+Buccaneers+20131111">4,721 available from $42</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/27">Raymond James Stadium</a></td>
</tr>
<tr class="evenrow"><td colspan="5">Bye: <a href="http://espn.go.com/nfl/team/_/name/cle/cleveland-browns">Cleveland</a>, <a href="http://espn.go.com/nfl/team/_/name/kc/kansas-city-chiefs">Kansas City</a>, <a href="http://espn.go.com/nfl/team/_/name/ne/new-england-patriots">New England</a>, <a href="http://espn.go.com/nfl/team/_/name/nyj/new-york-jets">NY Jets</a></td></tr></tbody></table><table class="tablehead" cellpadding="3" cellspacing="1"><tbody><tr class="stathead"><td colspan="5"><a name="11" style="color:#FFFFFF;"></a>Week 11<span><a href="#top">back to top �</a></span></td></tr>
<tr class="colhead">
<td width="170">THU, NOV 14</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-11 team-28-10"><td><a href="http://espn.go.com/nfl/team/_/name/ind/indianapolis-colts">Indianapolis</a> at <a href="http://espn.go.com/nfl/team/_/name/ten/tennessee-titans">Tennessee</a></td>
<td>8:25 PM</td>
<td align="center">NFL</td><td><a href="http://www.stubhub.com/tennessee-titans-nashville-lp-field-14-11-2013-4270688?gcid=C12289x445&amp;keyword=NFL+Schedule+Tennessee+Titans+20131114">4,561 available from $24</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/10">LP Field</a></td>
</tr>
<tr class="colhead">
<td width="170">SUN, NOV 17</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-20 team-28-2"><td><a href="http://espn.go.com/nfl/team/_/name/nyj/new-york-jets">NY Jets</a> at <a href="http://espn.go.com/nfl/team/_/name/buf/buffalo-bills">Buffalo</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/buffalo-bills-orchard-park-ralph-wilson-stadium-17-11-2013-4270669?gcid=C12289x445&amp;keyword=NFL+Schedule+Buffalo+Bills+20131117">7,477 available from $24</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/2">Ralph Wilson Stadium</a></td>
</tr>
<tr class="evenrow team-28-33 team-28-3"><td><a href="http://espn.go.com/nfl/team/_/name/bal/baltimore-ravens">Baltimore</a> at <a href="http://espn.go.com/nfl/team/_/name/chi/chicago-bears">Chicago</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/chicago-bears-chicago-soldier-field-17-11-2013-4271119?gcid=C12289x445&amp;keyword=NFL+Schedule+Chicago+Bears+20131117">5,759 available from $85</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/3">Soldier Field</a></td>
</tr>
<tr class="oddrow team-28-5 team-28-4"><td><a href="http://espn.go.com/nfl/team/_/name/cle/cleveland-browns">Cleveland</a> at <a href="http://espn.go.com/nfl/team/_/name/cin/cincinnati-bengals">Cincinnati</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/cincinnati-bengals-cincinnati-paul-brown-stadium-17-11-2013-4271217?gcid=C12289x445&amp;keyword=NFL+Schedule+Cincinnati+Bengals+20131117">8,381 available from $37</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/4">Paul Brown Stadium</a></td>
</tr>
<tr class="evenrow team-28-1 team-28-27"><td><a href="http://espn.go.com/nfl/team/_/name/atl/atlanta-falcons">Atlanta</a> at <a href="http://espn.go.com/nfl/team/_/name/tb/tampa-bay-buccaneers">Tampa Bay</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/tampa-bay-buccaneers-tampa-raymond-james-stadium-17-11-2013-4270690?gcid=C12289x445&amp;keyword=NFL+Schedule+Tampa+Bay+Buccaneers+20131117">5,280 available from $34</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/27">Raymond James Stadium</a></td>
</tr>
<tr class="oddrow team-28-22 team-28-30"><td><a href="http://espn.go.com/nfl/team/_/name/ari/arizona-cardinals">Arizona</a> at <a href="http://espn.go.com/nfl/team/_/name/jac/jacksonville-jaguars">Jacksonville</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/jacksonville-jaguars-jacksonville-everbank-field-17-11-2013-4271254?gcid=C12289x445&amp;keyword=NFL+Schedule+Jacksonville+Jaguars+20131117">3,417 available from $33</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/30">EverBank Field</a></td>
</tr>
<tr class="evenrow team-28-13 team-28-34"><td><a href="http://espn.go.com/nfl/team/_/name/oak/oakland-raiders">Oakland</a> at <a href="http://espn.go.com/nfl/team/_/name/hou/houston-texans">Houston</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/houston-texans-houston-reliant-stadium-17-11-2013-4271239?gcid=C12289x445&amp;keyword=NFL+Schedule+Houston+Texans+20131117">5,843 available from $39</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/34">Reliant Stadium</a></td>
</tr>
<tr class="oddrow team-28-24 team-28-15"><td><a href="http://espn.go.com/nfl/team/_/name/sd/san-diego-chargers">San Diego</a> at <a href="http://espn.go.com/nfl/team/_/name/mia/miami-dolphins">Miami</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/miami-dolphins-miami-gardens-sun-life-stadium-17-11-2013-4270647?gcid=C12289x445&amp;keyword=NFL+Schedule+Miami+Dolphins+20131117">5,465 available from $26</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/15">Sun Life Stadium</a></td>
</tr>
<tr class="evenrow team-28-28 team-28-21"><td><a href="http://espn.go.com/nfl/team/_/name/wsh/washington-redskins">Washington</a> at <a href="http://espn.go.com/nfl/team/_/name/phi/philadelphia-eagles">Philadelphia</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/philadelphia-eagles-philadelphia-lincoln-financial-field-17-11-2013-4271205?gcid=C12289x445&amp;keyword=NFL+Schedule+Philadelphia+Eagles+20131117">5,926 available from $65</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/21">Lincoln Financial Field</a></td>
</tr>
<tr class="oddrow team-28-8 team-28-23"><td><a href="http://espn.go.com/nfl/team/_/name/det/detroit-lions">Detroit</a> at <a href="http://espn.go.com/nfl/team/_/name/pit/pittsburgh-steelers">Pittsburgh</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/pittsburgh-steelers-pittsburgh-heinz-field-17-11-2013-4270675?gcid=C12289x445&amp;keyword=NFL+Schedule+Pittsburgh+Steelers+20131117">4,282 available from $60</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/23">Heinz Field</a></td>
</tr>
<tr class="evenrow team-28-12 team-28-7"><td><a href="http://espn.go.com/nfl/team/_/name/kc/kansas-city-chiefs">Kansas City</a> at <a href="http://espn.go.com/nfl/team/_/name/den/denver-broncos">Denver</a></td>
<td>4:05 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/denver-broncos-denver-sports-authority-field-at-mile-high-17-11-2013-4271120?gcid=C12289x445&amp;keyword=NFL+Schedule+Denver+Broncos+20131117">3,109 available from $56</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/7">Sports Authority Field at Mile High</a></td>
</tr>
<tr class="oddrow team-28-16 team-28-26"><td><a href="http://espn.go.com/nfl/team/_/name/min/minnesota-vikings">Minnesota</a> at <a href="http://espn.go.com/nfl/team/_/name/sea/seattle-seahawks">Seattle</a></td>
<td>4:25 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/seattle-seahawks-seattle-centurylink-field-17-11-2013-4271133?gcid=C12289x445&amp;keyword=NFL+Schedule+Seattle+Seahawks+20131117">4,353 available from $87</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/26">CenturyLink Field</a></td>
</tr>
<tr class="evenrow team-28-25 team-28-18"><td><a href="http://espn.go.com/nfl/team/_/name/sf/san-francisco-49ers">San Francisco</a> at <a href="http://espn.go.com/nfl/team/_/name/no/new-orleans-saints">New Orleans</a></td>
<td>4:25 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/new-orleans-saints-new-orleans-mercedes-benz-superdome-17-11-2013-4271201?gcid=C12289x445&amp;keyword=NFL+Schedule+New+Orleans+Saints+20131117">2,910 available from $129</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/18">Mercedes-Benz Superdome</a></td>
</tr>
<tr class="oddrow team-28-9 team-28-19"><td><a href="http://espn.go.com/nfl/team/_/name/gb/green-bay-packers">Green Bay</a> at <a href="http://espn.go.com/nfl/team/_/name/nyg/new-york-giants">NY Giants</a></td>
<td>8:30 PM</td>
<td align="center">NBC</td><td><a href="http://www.stubhub.com/new-york-giants-east-rutherford-metlife-stadium-17-11-2013-4271177?gcid=C12289x445&amp;keyword=NFL+Schedule+New+York+Giants+20131117">7,316 available from $45</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/19">MetLife Stadium</a></td>
</tr>
<tr class="colhead">
<td width="170">MON, NOV 18</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-17 team-28-29"><td><a href="http://espn.go.com/nfl/team/_/name/ne/new-england-patriots">New England</a> at <a href="http://espn.go.com/nfl/team/_/name/car/carolina-panthers">Carolina</a></td>
<td>8:30 PM</td>
<td align="center"><a href="http://sports.espn.go.com/espntv/espnNetwork?networkID=1"><img src="http://a.espncdn.com/i/scoreboard/networkLogo_espn.gif" width="55" height="14"></a><a href="http://espn.go.com/watchespn/?channel=espn&amp;launchPlayer=true"><img src="http://a.espncdn.com/i/scoreboard/watchespn-55x12.png"></a></td><td><a href="http://www.stubhub.com/carolina-panthers-charlotte-bank-of-america-stadium-18-11-2013-4270707?gcid=C12289x445&amp;keyword=NFL+Schedule+Carolina+Panthers+20131118">6,011 available from $48</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/29">Bank of America Stadium</a></td>
</tr>
<tr class="evenrow"><td colspan="5">Bye: <a href="http://espn.go.com/nfl/team/_/name/dal/dallas-cowboys">Dallas</a>, <a href="http://espn.go.com/nfl/team/_/name/stl/st.-louis-rams">St. Louis</a></td></tr></tbody></table><table class="tablehead" cellpadding="3" cellspacing="1"><tbody><tr class="stathead"><td colspan="5"><a name="12" style="color:#FFFFFF;"></a>Week 12<span><a href="#top">back to top �</a></span></td></tr>
<tr class="colhead">
<td width="170">THU, NOV 21</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-18 team-28-1"><td><a href="http://espn.go.com/nfl/team/_/name/no/new-orleans-saints">New Orleans</a> at <a href="http://espn.go.com/nfl/team/_/name/atl/atlanta-falcons">Atlanta</a></td>
<td>8:25 PM</td>
<td align="center">NFL</td><td><a href="http://www.stubhub.com/atlanta-falcons-atlanta-georgia-dome-21-11-2013-4271156?gcid=C12289x445&amp;keyword=NFL+Schedule+Atlanta+Falcons+20131121">7,938 available from $53</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/1">Georgia Dome</a></td>
</tr>
<tr class="colhead">
<td width="170">SUN, NOV 24</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-23 team-28-5"><td><a href="http://espn.go.com/nfl/team/_/name/pit/pittsburgh-steelers">Pittsburgh</a> at <a href="http://espn.go.com/nfl/team/_/name/cle/cleveland-browns">Cleveland</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/cleveland-browns-cleveland-firstenergy-stadium-24-11-2013-4270670?gcid=C12289x445&amp;keyword=NFL+Schedule+Cleveland+Browns+20131124">7,245 available from $55</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/5">FirstEnergy Stadium</a></td>
</tr>
<tr class="evenrow team-28-27 team-28-8"><td><a href="http://espn.go.com/nfl/team/_/name/tb/tampa-bay-buccaneers">Tampa Bay</a> at <a href="http://espn.go.com/nfl/team/_/name/det/detroit-lions">Detroit</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/detroit-lions-detroit-ford-field-24-11-2013-4271134?gcid=C12289x445&amp;keyword=NFL+Schedule+Detroit+Lions+20131124">14,856 available from $32</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/8">Ford Field</a></td>
</tr>
<tr class="oddrow team-28-16 team-28-9"><td><a href="http://espn.go.com/nfl/team/_/name/min/minnesota-vikings">Minnesota</a> at <a href="http://espn.go.com/nfl/team/_/name/gb/green-bay-packers">Green Bay</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/green-bay-packers-green-bay-lambeau-field-24-11-2013-4271140?gcid=C12289x445&amp;keyword=NFL+Schedule+Green+Bay+Packers+20131124">4,245 available from $93</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/9">Lambeau Field</a></td>
</tr>
<tr class="evenrow team-28-24 team-28-12"><td><a href="http://espn.go.com/nfl/team/_/name/sd/san-diego-chargers">San Diego</a> at <a href="http://espn.go.com/nfl/team/_/name/kc/kansas-city-chiefs">Kansas City</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/kansas-city-chiefs-kansas-city-arrowhead-stadium-24-11-2013-4271194?gcid=C12289x445&amp;keyword=NFL+Schedule+Kansas+City+Chiefs+20131124">10,997 available from $19</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/12">Arrowhead Stadium</a></td>
</tr>
<tr class="oddrow team-28-3 team-28-14"><td><a href="http://espn.go.com/nfl/team/_/name/chi/chicago-bears">Chicago</a> at <a href="http://espn.go.com/nfl/team/_/name/stl/st.-louis-rams">St. Louis</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/st-louis-rams-saint-louis-edward-jones-dome-24-11-2013-4271277?gcid=C12289x445&amp;keyword=NFL+Schedule+St.+Louis+Rams+20131124">6,643 available from $80</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/14">Edward Jones Dome</a></td>
</tr>
<tr class="evenrow team-28-29 team-28-15"><td><a href="http://espn.go.com/nfl/team/_/name/car/carolina-panthers">Carolina</a> at <a href="http://espn.go.com/nfl/team/_/name/mia/miami-dolphins">Miami</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/miami-dolphins-miami-gardens-sun-life-stadium-24-11-2013-4271166?gcid=C12289x445&amp;keyword=NFL+Schedule+Miami+Dolphins+20131124">5,557 available from $30</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/15">Sun Life Stadium</a></td>
</tr>
<tr class="oddrow team-28-20 team-28-33"><td><a href="http://espn.go.com/nfl/team/_/name/nyj/new-york-jets">NY Jets</a> at <a href="http://espn.go.com/nfl/team/_/name/bal/baltimore-ravens">Baltimore</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/baltimore-ravens-baltimore-m-t-bank-stadium-24-11-2013-4271193?gcid=C12289x445&amp;keyword=NFL+Schedule+Baltimore+Ravens+20131124">3,825 available from $100</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/33">M&amp;T Bank Stadium</a></td>
</tr>
<tr class="evenrow team-28-30 team-28-34"><td><a href="http://espn.go.com/nfl/team/_/name/jac/jacksonville-jaguars">Jacksonville</a> at <a href="http://espn.go.com/nfl/team/_/name/hou/houston-texans">Houston</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/houston-texans-houston-reliant-stadium-24-11-2013-4271242?gcid=C12289x445&amp;keyword=NFL+Schedule+Houston+Texans+20131124">6,465 available from $34</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/34">Reliant Stadium</a></td>
</tr>
<tr class="oddrow team-28-11 team-28-22"><td><a href="http://espn.go.com/nfl/team/_/name/ind/indianapolis-colts">Indianapolis</a> at <a href="http://espn.go.com/nfl/team/_/name/ari/arizona-cardinals">Arizona</a></td>
<td>4:05 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/arizona-cardinals-glendale-university-of-phoenix-stadium-24-11-2013-4270656?gcid=C12289x445&amp;keyword=NFL+Schedule+Arizona+Cardinals+20131124">8,012 available from $5</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/22">U of Phoenix Stadium</a></td>
</tr>
<tr class="evenrow team-28-10 team-28-13"><td><a href="http://espn.go.com/nfl/team/_/name/ten/tennessee-titans">Tennessee</a> at <a href="http://espn.go.com/nfl/team/_/name/oak/oakland-raiders">Oakland</a></td>
<td>4:05 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/oakland-raiders-oakland-o-co-coliseum-24-11-2013-4271161?gcid=C12289x445&amp;keyword=NFL+Schedule+Oakland+Raiders+20131124">7,223 available from $20</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/13">O.co Coliseum</a></td>
</tr>
<tr class="oddrow team-28-6 team-28-19"><td><a href="http://espn.go.com/nfl/team/_/name/dal/dallas-cowboys">Dallas</a> at <a href="http://espn.go.com/nfl/team/_/name/nyg/new-york-giants">NY Giants</a></td>
<td>4:25 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/new-york-giants-east-rutherford-metlife-stadium-24-11-2013-4271179?gcid=C12289x445&amp;keyword=NFL+Schedule+New+York+Giants+20131124">6,638 available from $49</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/19">MetLife Stadium</a></td>
</tr>
<tr class="evenrow team-28-7 team-28-17"><td><a href="http://espn.go.com/nfl/team/_/name/den/denver-broncos">Denver</a> at <a href="http://espn.go.com/nfl/team/_/name/ne/new-england-patriots">New England</a></td>
<td>8:30 PM</td>
<td align="center">NBC</td><td><a href="http://www.stubhub.com/new-england-patriots-foxborough-gillette-stadium-24-11-2013-4271151?gcid=C12289x445&amp;keyword=NFL+Schedule+New+England+Patriots+20131124">3,151 available from $176</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/17">Gillette Stadium</a></td>
</tr>
<tr class="colhead">
<td width="170">MON, NOV 25</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-25 team-28-28"><td><a href="http://espn.go.com/nfl/team/_/name/sf/san-francisco-49ers">San Francisco</a> at <a href="http://espn.go.com/nfl/team/_/name/wsh/washington-redskins">Washington</a></td>
<td>8:30 PM</td>
<td align="center"><a href="http://sports.espn.go.com/espntv/espnNetwork?networkID=1"><img src="http://a.espncdn.com/i/scoreboard/networkLogo_espn.gif" width="55" height="14"></a><a href="http://espn.go.com/watchespn/?channel=espn&amp;launchPlayer=true"><img src="http://a.espncdn.com/i/scoreboard/watchespn-55x12.png"></a></td><td><a href="http://www.stubhub.com/washington-redskins-landover-fedexfield-25-11-2013-4271220?gcid=C12289x445&amp;keyword=NFL+Schedule+Washington+Redskins+20131125">9,542 available from $39</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/28">FedEx Field</a></td>
</tr>
<tr class="evenrow"><td colspan="5">Bye: <a href="http://espn.go.com/nfl/team/_/name/buf/buffalo-bills">Buffalo</a>, <a href="http://espn.go.com/nfl/team/_/name/cin/cincinnati-bengals">Cincinnati</a>, <a href="http://espn.go.com/nfl/team/_/name/phi/philadelphia-eagles">Philadelphia</a>, <a href="http://espn.go.com/nfl/team/_/name/sea/seattle-seahawks">Seattle</a></td></tr></tbody></table><table class="tablehead" cellpadding="3" cellspacing="1"><tbody><tr class="stathead"><td colspan="5"><a name="13" style="color:#FFFFFF;"></a>Week 13<span><a href="#top">back to top �</a></span></td></tr>
<tr class="colhead">
<td width="170">THU, NOV 28</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-9 team-28-8"><td><a href="http://espn.go.com/nfl/team/_/name/gb/green-bay-packers">Green Bay</a> at <a href="http://espn.go.com/nfl/team/_/name/det/detroit-lions">Detroit</a></td>
<td>12:30 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/detroit-lions-detroit-ford-field-28-11-2013-4271136?gcid=C12289x445&amp;keyword=NFL+Schedule+Detroit+Lions+20131128">13,197 available from $85</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/8">Ford Field</a></td>
</tr>
<tr class="evenrow team-28-13 team-28-6"><td><a href="http://espn.go.com/nfl/team/_/name/oak/oakland-raiders">Oakland</a> at <a href="http://espn.go.com/nfl/team/_/name/dal/dallas-cowboys">Dallas</a></td>
<td>4:30 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/dallas-cowboys-arlington-cowboys-stadium-28-11-2013-4271165?gcid=C12289x445&amp;keyword=NFL+Schedule+Dallas+Cowboys+20131128">14,110 available from $29</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/6">AT&amp;T Stadium</a></td>
</tr>
<tr class="oddrow team-28-23 team-28-33"><td><a href="http://espn.go.com/nfl/team/_/name/pit/pittsburgh-steelers">Pittsburgh</a> at <a href="http://espn.go.com/nfl/team/_/name/bal/baltimore-ravens">Baltimore</a></td>
<td>8:30 PM</td>
<td align="center">NBC</td><td><a href="http://www.stubhub.com/baltimore-ravens-baltimore-m-t-bank-stadium-28-11-2013-4271197?gcid=C12289x445&amp;keyword=NFL+Schedule+Baltimore+Ravens+20131128">3,323 available from $184</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/33">M&amp;T Bank Stadium</a></td>
</tr>
<tr class="colhead">
<td width="170">SUN, DEC 1</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-27 team-28-29"><td><a href="http://espn.go.com/nfl/team/_/name/tb/tampa-bay-buccaneers">Tampa Bay</a> at <a href="http://espn.go.com/nfl/team/_/name/car/carolina-panthers">Carolina</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/carolina-panthers-charlotte-bank-of-america-stadium-1-12-2013-4270708?gcid=C12289x445&amp;keyword=NFL+Schedule+Carolina+Panthers+20131201">5,304 available from $30</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/29">Bank of America Stadium</a></td>
</tr>
<tr class="evenrow team-28-30 team-28-5"><td><a href="http://espn.go.com/nfl/team/_/name/jac/jacksonville-jaguars">Jacksonville</a> at <a href="http://espn.go.com/nfl/team/_/name/cle/cleveland-browns">Cleveland</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/cleveland-browns-cleveland-firstenergy-stadium-1-12-2013-4270671?gcid=C12289x445&amp;keyword=NFL+Schedule+Cleveland+Browns+20131201">6,637 available from $13</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/5">FirstEnergy Stadium</a></td>
</tr>
<tr class="oddrow team-28-10 team-28-11"><td><a href="http://espn.go.com/nfl/team/_/name/ten/tennessee-titans">Tennessee</a> at <a href="http://espn.go.com/nfl/team/_/name/ind/indianapolis-colts">Indianapolis</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/indianapolis-colts-indianapolis-lucas-oil-stadium-1-12-2013-4271259?gcid=C12289x445&amp;keyword=NFL+Schedule+Indianapolis+Colts+20131201">5,645 available from $26</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/11">Lucas Oil Stadium</a></td>
</tr>
<tr class="evenrow team-28-7 team-28-12"><td><a href="http://espn.go.com/nfl/team/_/name/den/denver-broncos">Denver</a> at <a href="http://espn.go.com/nfl/team/_/name/kc/kansas-city-chiefs">Kansas City</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/kansas-city-chiefs-kansas-city-arrowhead-stadium-1-12-2013-4271198?gcid=C12289x445&amp;keyword=NFL+Schedule+Kansas+City+Chiefs+20131201">10,525 available from $35</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/12">Arrowhead Stadium</a></td>
</tr>
<tr class="oddrow team-28-3 team-28-16"><td><a href="http://espn.go.com/nfl/team/_/name/chi/chicago-bears">Chicago</a> at <a href="http://espn.go.com/nfl/team/_/name/min/minnesota-vikings">Minnesota</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/minnesota-vikings-minneapolis-hubert-h--humphrey-metrodome-1-12-2013-4270635?gcid=C12289x445&amp;keyword=NFL+Schedule+Minnesota+Vikings+20131201">7,729 available from $49</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/16">Mall of America Field</a></td>
</tr>
<tr class="evenrow team-28-15 team-28-20"><td><a href="http://espn.go.com/nfl/team/_/name/mia/miami-dolphins">Miami</a> at <a href="http://espn.go.com/nfl/team/_/name/nyj/new-york-jets">NY Jets</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/new-york-jets-east-rutherford-metlife-stadium-1-12-2013-4271123?gcid=C12289x445&amp;keyword=NFL+Schedule+New+York+Jets+20131201">9,193 available from $34</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/20">MetLife Stadium</a></td>
</tr>
<tr class="oddrow team-28-22 team-28-21"><td><a href="http://espn.go.com/nfl/team/_/name/ari/arizona-cardinals">Arizona</a> at <a href="http://espn.go.com/nfl/team/_/name/phi/philadelphia-eagles">Philadelphia</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/philadelphia-eagles-philadelphia-lincoln-financial-field-1-12-2013-4270652?gcid=C12289x445&amp;keyword=NFL+Schedule+Philadelphia+Eagles+20131201">6,125 available from $60</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/21">Lincoln Financial Field</a></td>
</tr>
<tr class="evenrow team-28-1 team-28-2"><td><a href="http://espn.go.com/nfl/team/_/name/atl/atlanta-falcons">Atlanta</a> at <a href="http://espn.go.com/nfl/team/_/name/buf/buffalo-bills">Buffalo</a></td>
<td>4:05 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/buffalo-bills-toronto-rogers-centre-1-12-2013-4271240?gcid=C12289x445&amp;keyword=NFL+Schedule+Buffalo+Bills+20131201">1,045 available from $87</a></td>
<td>Rogers Centre</td>
</tr>
<tr class="oddrow team-28-14 team-28-25"><td><a href="http://espn.go.com/nfl/team/_/name/stl/st.-louis-rams">St. Louis</a> at <a href="http://espn.go.com/nfl/team/_/name/sf/san-francisco-49ers">San Francisco</a></td>
<td>4:05 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/san-francisco-49ers-san-francisco-candlestick-park-1-12-2013-4270598?gcid=C12289x445&amp;keyword=NFL+Schedule+San+Francisco+49ers+20131201">12,967 available from $67</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/25">Candlestick Park</a></td>
</tr>
<tr class="evenrow team-28-17 team-28-34"><td><a href="http://espn.go.com/nfl/team/_/name/ne/new-england-patriots">New England</a> at <a href="http://espn.go.com/nfl/team/_/name/hou/houston-texans">Houston</a></td>
<td>4:25 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/houston-texans-houston-reliant-stadium-1-12-2013-4271243?gcid=C12289x445&amp;keyword=NFL+Schedule+Houston+Texans+20131201">4,382 available from $52</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/34">Reliant Stadium</a></td>
</tr>
<tr class="oddrow team-28-4 team-28-24"><td><a href="http://espn.go.com/nfl/team/_/name/cin/cincinnati-bengals">Cincinnati</a> at <a href="http://espn.go.com/nfl/team/_/name/sd/san-diego-chargers">San Diego</a></td>
<td>4:25 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/san-diego-chargers-san-diego-qualcomm-stadium-1-12-2013-4271138?gcid=C12289x445&amp;keyword=NFL+Schedule+San+Diego+Chargers+20131201">11,821 available from $30</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/24">Qualcomm Stadium</a></td>
</tr>
<tr class="evenrow team-28-19 team-28-28"><td><a href="http://espn.go.com/nfl/team/_/name/nyg/new-york-giants">NY Giants</a> at <a href="http://espn.go.com/nfl/team/_/name/wsh/washington-redskins">Washington</a></td>
<td>8:30 PM</td>
<td align="center">NBC</td><td><a href="http://www.stubhub.com/washington-redskins-landover-fedexfield-1-12-2013-4271223?gcid=C12289x445&amp;keyword=NFL+Schedule+Washington+Redskins+20131201">11,098 available from $37</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/28">FedEx Field</a></td>
</tr>
<tr class="colhead">
<td width="170">MON, DEC 2</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-18 team-28-26"><td><a href="http://espn.go.com/nfl/team/_/name/no/new-orleans-saints">New Orleans</a> at <a href="http://espn.go.com/nfl/team/_/name/sea/seattle-seahawks">Seattle</a></td>
<td>8:30 PM</td>
<td align="center"><a href="http://sports.espn.go.com/espntv/espnNetwork?networkID=1"><img src="http://a.espncdn.com/i/scoreboard/networkLogo_espn.gif" width="55" height="14"></a><a href="http://espn.go.com/watchespn/?channel=espn&amp;launchPlayer=true"><img src="http://a.espncdn.com/i/scoreboard/watchespn-55x12.png"></a></td><td><a href="http://www.stubhub.com/seattle-seahawks-seattle-centurylink-field-2-12-2013-4271135?gcid=C12289x445&amp;keyword=NFL+Schedule+Seattle+Seahawks+20131202">6,044 available from $98</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/26">CenturyLink Field</a></td>
</tr>
</tbody></table><table class="tablehead" cellpadding="3" cellspacing="1"><tbody><tr class="stathead"><td colspan="5"><a name="14" style="color:#FFFFFF;"></a>Week 14<span><a href="#top">back to top �</a></span></td></tr>
<tr class="colhead">
<td width="170">THU, DEC 5</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-34 team-28-30"><td><a href="http://espn.go.com/nfl/team/_/name/hou/houston-texans">Houston</a> at <a href="http://espn.go.com/nfl/team/_/name/jac/jacksonville-jaguars">Jacksonville</a></td>
<td>8:25 PM</td>
<td align="center">NFL</td><td><a href="http://www.stubhub.com/jacksonville-jaguars-jacksonville-everbank-field-5-12-2013-4271255?gcid=C12289x445&amp;keyword=NFL+Schedule+Jacksonville+Jaguars+20131205">3,234 available from $43</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/30">EverBank Field</a></td>
</tr>
<tr class="colhead">
<td width="170">SUN, DEC 8</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-11 team-28-4"><td><a href="http://espn.go.com/nfl/team/_/name/ind/indianapolis-colts">Indianapolis</a> at <a href="http://espn.go.com/nfl/team/_/name/cin/cincinnati-bengals">Cincinnati</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/cincinnati-bengals-cincinnati-paul-brown-stadium-8-12-2013-4271219?gcid=C12289x445&amp;keyword=NFL+Schedule+Cincinnati+Bengals+20131208">7,806 available from $39</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/4">Paul Brown Stadium</a></td>
</tr>
<tr class="evenrow team-28-2 team-28-27"><td><a href="http://espn.go.com/nfl/team/_/name/buf/buffalo-bills">Buffalo</a> at <a href="http://espn.go.com/nfl/team/_/name/tb/tampa-bay-buccaneers">Tampa Bay</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/tampa-bay-buccaneers-tampa-raymond-james-stadium-8-12-2013-4270691?gcid=C12289x445&amp;keyword=NFL+Schedule+Tampa+Bay+Buccaneers+20131208">4,842 available from $30</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/27">Raymond James Stadium</a></td>
</tr>
<tr class="oddrow team-28-12 team-28-28"><td><a href="http://espn.go.com/nfl/team/_/name/kc/kansas-city-chiefs">Kansas City</a> at <a href="http://espn.go.com/nfl/team/_/name/wsh/washington-redskins">Washington</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/washington-redskins-landover-fedexfield-8-12-2013-4271225?gcid=C12289x445&amp;keyword=NFL+Schedule+Washington+Redskins+20131208">11,715 available from $27</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/28">FedEx Field</a></td>
</tr>
<tr class="evenrow team-28-16 team-28-33"><td><a href="http://espn.go.com/nfl/team/_/name/min/minnesota-vikings">Minnesota</a> at <a href="http://espn.go.com/nfl/team/_/name/bal/baltimore-ravens">Baltimore</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/baltimore-ravens-baltimore-m-t-bank-stadium-8-12-2013-4271199?gcid=C12289x445&amp;keyword=NFL+Schedule+Baltimore+Ravens+20131208">3,691 available from $107</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/33">M&amp;T Bank Stadium</a></td>
</tr>
<tr class="oddrow team-28-5 team-28-17"><td><a href="http://espn.go.com/nfl/team/_/name/cle/cleveland-browns">Cleveland</a> at <a href="http://espn.go.com/nfl/team/_/name/ne/new-england-patriots">New England</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/new-england-patriots-foxborough-gillette-stadium-8-12-2013-4270631?gcid=C12289x445&amp;keyword=NFL+Schedule+New+England+Patriots+20131208">3,173 available from $107</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/17">Gillette Stadium</a></td>
</tr>
<tr class="evenrow team-28-29 team-28-18"><td><a href="http://espn.go.com/nfl/team/_/name/car/carolina-panthers">Carolina</a> at <a href="http://espn.go.com/nfl/team/_/name/no/new-orleans-saints">New Orleans</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/new-orleans-saints-new-orleans-mercedes-benz-superdome-8-12-2013-4271204?gcid=C12289x445&amp;keyword=NFL+Schedule+New+Orleans+Saints+20131208">4,037 available from $63</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/18">Mercedes-Benz Superdome</a></td>
</tr>
<tr class="oddrow team-28-13 team-28-20"><td><a href="http://espn.go.com/nfl/team/_/name/oak/oakland-raiders">Oakland</a> at <a href="http://espn.go.com/nfl/team/_/name/nyj/new-york-jets">NY Jets</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/new-york-jets-east-rutherford-metlife-stadium-8-12-2013-4271127?gcid=C12289x445&amp;keyword=NFL+Schedule+New+York+Jets+20131208">9,628 available from $29</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/20">MetLife Stadium</a></td>
</tr>
<tr class="evenrow team-28-8 team-28-21"><td><a href="http://espn.go.com/nfl/team/_/name/det/detroit-lions">Detroit</a> at <a href="http://espn.go.com/nfl/team/_/name/phi/philadelphia-eagles">Philadelphia</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/philadelphia-eagles-philadelphia-lincoln-financial-field-8-12-2013-4271207?gcid=C12289x445&amp;keyword=NFL+Schedule+Philadelphia+Eagles+20131208">6,122 available from $60</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/21">Lincoln Financial Field</a></td>
</tr>
<tr class="oddrow team-28-15 team-28-23"><td><a href="http://espn.go.com/nfl/team/_/name/mia/miami-dolphins">Miami</a> at <a href="http://espn.go.com/nfl/team/_/name/pit/pittsburgh-steelers">Pittsburgh</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/pittsburgh-steelers-pittsburgh-heinz-field-8-12-2013-4270676?gcid=C12289x445&amp;keyword=NFL+Schedule+Pittsburgh+Steelers+20131208">4,618 available from $57</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/23">Heinz Field</a></td>
</tr>
<tr class="evenrow team-28-10 team-28-7"><td><a href="http://espn.go.com/nfl/team/_/name/ten/tennessee-titans">Tennessee</a> at <a href="http://espn.go.com/nfl/team/_/name/den/denver-broncos">Denver</a></td>
<td>4:05 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/denver-broncos-denver-sports-authority-field-at-mile-high-8-12-2013-4271125?gcid=C12289x445&amp;keyword=NFL+Schedule+Denver+Broncos+20131208">3,940 available from $56</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/7">Sports Authority Field at Mile High</a></td>
</tr>
<tr class="oddrow team-28-19 team-28-24"><td><a href="http://espn.go.com/nfl/team/_/name/nyg/new-york-giants">NY Giants</a> at <a href="http://espn.go.com/nfl/team/_/name/sd/san-diego-chargers">San Diego</a></td>
<td>4:25 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/san-diego-chargers-san-diego-qualcomm-stadium-8-12-2013-4270621?gcid=C12289x445&amp;keyword=NFL+Schedule+San+Diego+Chargers+20131208">7,855 available from $67</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/24">Qualcomm Stadium</a></td>
</tr>
<tr class="evenrow team-28-26 team-28-25"><td><a href="http://espn.go.com/nfl/team/_/name/sea/seattle-seahawks">Seattle</a> at <a href="http://espn.go.com/nfl/team/_/name/sf/san-francisco-49ers">San Francisco</a></td>
<td>4:25 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/san-francisco-49ers-san-francisco-candlestick-park-8-12-2013-4271112?gcid=C12289x445&amp;keyword=NFL+Schedule+San+Francisco+49ers+20131208">10,459 available from $87</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/25">Candlestick Park</a></td>
</tr>
<tr class="oddrow team-28-14 team-28-22"><td><a href="http://espn.go.com/nfl/team/_/name/stl/st.-louis-rams">St. Louis</a> at <a href="http://espn.go.com/nfl/team/_/name/ari/arizona-cardinals">Arizona</a></td>
<td>4:25 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/arizona-cardinals-glendale-university-of-phoenix-stadium-8-12-2013-4270659?gcid=C12289x445&amp;keyword=NFL+Schedule+Arizona+Cardinals+20131208">8,582 available from $5</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/22">U of Phoenix Stadium</a></td>
</tr>
<tr class="evenrow team-28-1 team-28-9"><td><a href="http://espn.go.com/nfl/team/_/name/atl/atlanta-falcons">Atlanta</a> at <a href="http://espn.go.com/nfl/team/_/name/gb/green-bay-packers">Green Bay</a></td>
<td>8:30 PM</td>
<td align="center">NBC</td><td><a href="http://www.stubhub.com/green-bay-packers-green-bay-lambeau-field-8-12-2013-4271141?gcid=C12289x445&amp;keyword=NFL+Schedule+Green+Bay+Packers+20131208">4,870 available from $37</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/9">Lambeau Field</a></td>
</tr>
<tr class="colhead">
<td width="170">MON, DEC 9</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-6 team-28-3"><td><a href="http://espn.go.com/nfl/team/_/name/dal/dallas-cowboys">Dallas</a> at <a href="http://espn.go.com/nfl/team/_/name/chi/chicago-bears">Chicago</a></td>
<td>8:30 PM</td>
<td align="center"><a href="http://sports.espn.go.com/espntv/espnNetwork?networkID=1"><img src="http://a.espncdn.com/i/scoreboard/networkLogo_espn.gif" width="55" height="14"></a><a href="http://espn.go.com/watchespn/?channel=espn&amp;launchPlayer=true"><img src="http://a.espncdn.com/i/scoreboard/watchespn-55x12.png"></a></td><td><a href="http://www.stubhub.com/chicago-bears-chicago-soldier-field-9-12-2013-4271121?gcid=C12289x445&amp;keyword=NFL+Schedule+Chicago+Bears+20131209">5,914 available from $85</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/3">Soldier Field</a></td>
</tr>
</tbody></table><table class="tablehead" cellpadding="3" cellspacing="1"><tbody><tr class="stathead"><td colspan="5"><a name="15" style="color:#FFFFFF;"></a>Week 15<span><a href="#top">back to top �</a></span></td></tr>
<tr class="colhead">
<td width="170">THU, DEC 12</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-24 team-28-7"><td><a href="http://espn.go.com/nfl/team/_/name/sd/san-diego-chargers">San Diego</a> at <a href="http://espn.go.com/nfl/team/_/name/den/denver-broncos">Denver</a></td>
<td>8:25 PM</td>
<td align="center">NFL</td><td><a href="http://www.stubhub.com/denver-broncos-denver-sports-authority-field-at-mile-high-12-12-2013-4271128?gcid=C12289x445&amp;keyword=NFL+Schedule+Denver+Broncos+20131212">3,855 available from $56</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/7">Sports Authority Field at Mile High</a></td>
</tr>
<tr class="colhead">
<td width="170">SUN, DEC 15</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-28 team-28-1"><td><a href="http://espn.go.com/nfl/team/_/name/wsh/washington-redskins">Washington</a> at <a href="http://espn.go.com/nfl/team/_/name/atl/atlanta-falcons">Atlanta</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/atlanta-falcons-atlanta-georgia-dome-15-12-2013-4271158?gcid=C12289x445&amp;keyword=NFL+Schedule+Atlanta+Falcons+20131215">7,142 available from $41</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/1">Georgia Dome</a></td>
</tr>
<tr class="evenrow team-28-3 team-28-5"><td><a href="http://espn.go.com/nfl/team/_/name/chi/chicago-bears">Chicago</a> at <a href="http://espn.go.com/nfl/team/_/name/cle/cleveland-browns">Cleveland</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/cleveland-browns-cleveland-firstenergy-stadium-15-12-2013-4270672?gcid=C12289x445&amp;keyword=NFL+Schedule+Cleveland+Browns+20131215">7,603 available from $35</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/5">FirstEnergy Stadium</a></td>
</tr>
<tr class="oddrow team-28-22 team-28-10"><td><a href="http://espn.go.com/nfl/team/_/name/ari/arizona-cardinals">Arizona</a> at <a href="http://espn.go.com/nfl/team/_/name/ten/tennessee-titans">Tennessee</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/tennessee-titans-nashville-lp-field-15-12-2013-4270693?gcid=C12289x445&amp;keyword=NFL+Schedule+Tennessee+Titans+20131215">5,305 available from $19</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/10">LP Field</a></td>
</tr>
<tr class="evenrow team-28-34 team-28-11"><td><a href="http://espn.go.com/nfl/team/_/name/hou/houston-texans">Houston</a> at <a href="http://espn.go.com/nfl/team/_/name/ind/indianapolis-colts">Indianapolis</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/indianapolis-colts-indianapolis-lucas-oil-stadium-15-12-2013-4271261?gcid=C12289x445&amp;keyword=NFL+Schedule+Indianapolis+Colts+20131215">5,791 available from $26</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/11">Lucas Oil Stadium</a></td>
</tr>
<tr class="oddrow team-28-18 team-28-14"><td><a href="http://espn.go.com/nfl/team/_/name/no/new-orleans-saints">New Orleans</a> at <a href="http://espn.go.com/nfl/team/_/name/stl/st.-louis-rams">St. Louis</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/st-louis-rams-saint-louis-edward-jones-dome-15-12-2013-4271278?gcid=C12289x445&amp;keyword=NFL+Schedule+St.+Louis+Rams+20131215">8,317 available from $36</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/14">Edward Jones Dome</a></td>
</tr>
<tr class="evenrow team-28-17 team-28-15"><td><a href="http://espn.go.com/nfl/team/_/name/ne/new-england-patriots">New England</a> at <a href="http://espn.go.com/nfl/team/_/name/mia/miami-dolphins">Miami</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/miami-dolphins-miami-gardens-sun-life-stadium-15-12-2013-4271169?gcid=C12289x445&amp;keyword=NFL+Schedule+Miami+Dolphins+20131215">9,315 available from $37</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/15">Sun Life Stadium</a></td>
</tr>
<tr class="oddrow team-28-21 team-28-16"><td><a href="http://espn.go.com/nfl/team/_/name/phi/philadelphia-eagles">Philadelphia</a> at <a href="http://espn.go.com/nfl/team/_/name/min/minnesota-vikings">Minnesota</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/minnesota-vikings-minneapolis-hubert-h--humphrey-metrodome-15-12-2013-4270637?gcid=C12289x445&amp;keyword=NFL+Schedule+Minnesota+Vikings+20131215">8,460 available from $34</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/16">Mall of America Field</a></td>
</tr>
<tr class="evenrow team-28-26 team-28-19"><td><a href="http://espn.go.com/nfl/team/_/name/sea/seattle-seahawks">Seattle</a> at <a href="http://espn.go.com/nfl/team/_/name/nyg/new-york-giants">NY Giants</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/new-york-giants-east-rutherford-metlife-stadium-15-12-2013-4271181?gcid=C12289x445&amp;keyword=NFL+Schedule+New+York+Giants+20131215">6,747 available from $42</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/19">MetLife Stadium</a></td>
</tr>
<tr class="oddrow team-28-2 team-28-30"><td><a href="http://espn.go.com/nfl/team/_/name/buf/buffalo-bills">Buffalo</a> at <a href="http://espn.go.com/nfl/team/_/name/jac/jacksonville-jaguars">Jacksonville</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/jacksonville-jaguars-jacksonville-everbank-field-15-12-2013-4271257?gcid=C12289x445&amp;keyword=NFL+Schedule+Jacksonville+Jaguars+20131215">3,147 available from $34</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/30">EverBank Field</a></td>
</tr>
<tr class="evenrow team-28-25 team-28-27"><td><a href="http://espn.go.com/nfl/team/_/name/sf/san-francisco-49ers">San Francisco</a> at <a href="http://espn.go.com/nfl/team/_/name/tb/tampa-bay-buccaneers">Tampa Bay</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/tampa-bay-buccaneers-tampa-raymond-james-stadium-15-12-2013-4270694?gcid=C12289x445&amp;keyword=NFL+Schedule+Tampa+Bay+Buccaneers+20131215">5,599 available from $35</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/27">Raymond James Stadium</a></td>
</tr>
<tr class="oddrow team-28-20 team-28-29"><td><a href="http://espn.go.com/nfl/team/_/name/nyj/new-york-jets">NY Jets</a> at <a href="http://espn.go.com/nfl/team/_/name/car/carolina-panthers">Carolina</a></td>
<td>4:05 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/carolina-panthers-charlotte-bank-of-america-stadium-15-12-2013-4270709?gcid=C12289x445&amp;keyword=NFL+Schedule+Carolina+Panthers+20131215">5,385 available from $41</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/29">Bank of America Stadium</a></td>
</tr>
<tr class="evenrow team-28-12 team-28-13"><td><a href="http://espn.go.com/nfl/team/_/name/kc/kansas-city-chiefs">Kansas City</a> at <a href="http://espn.go.com/nfl/team/_/name/oak/oakland-raiders">Oakland</a></td>
<td>4:05 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/oakland-raiders-oakland-o-co-coliseum-15-12-2013-4270644?gcid=C12289x445&amp;keyword=NFL+Schedule+Oakland+Raiders+20131215">7,151 available from $22</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/13">O.co Coliseum</a></td>
</tr>
<tr class="oddrow team-28-9 team-28-6"><td><a href="http://espn.go.com/nfl/team/_/name/gb/green-bay-packers">Green Bay</a> at <a href="http://espn.go.com/nfl/team/_/name/dal/dallas-cowboys">Dallas</a></td>
<td>4:25 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/dallas-cowboys-arlington-cowboys-stadium-15-12-2013-4271167?gcid=C12289x445&amp;keyword=NFL+Schedule+Dallas+Cowboys+20131215">16,570 available from $50</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/6">AT&amp;T Stadium</a></td>
</tr>
<tr class="evenrow team-28-4 team-28-23"><td><a href="http://espn.go.com/nfl/team/_/name/cin/cincinnati-bengals">Cincinnati</a> at <a href="http://espn.go.com/nfl/team/_/name/pit/pittsburgh-steelers">Pittsburgh</a></td>
<td>8:30 PM</td>
<td align="center">NBC</td><td><a href="http://www.stubhub.com/pittsburgh-steelers-pittsburgh-heinz-field-15-12-2013-4270677?gcid=C12289x445&amp;keyword=NFL+Schedule+Pittsburgh+Steelers+20131215">5,676 available from $58</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/23">Heinz Field</a></td>
</tr>
<tr class="colhead">
<td width="170">MON, DEC 16</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-33 team-28-8"><td><a href="http://espn.go.com/nfl/team/_/name/bal/baltimore-ravens">Baltimore</a> at <a href="http://espn.go.com/nfl/team/_/name/det/detroit-lions">Detroit</a></td>
<td>8:30 PM</td>
<td align="center"><a href="http://sports.espn.go.com/espntv/espnNetwork?networkID=1"><img src="http://a.espncdn.com/i/scoreboard/networkLogo_espn.gif" width="55" height="14"></a><a href="http://espn.go.com/watchespn/?channel=espn&amp;launchPlayer=true"><img src="http://a.espncdn.com/i/scoreboard/watchespn-55x12.png"></a></td><td><a href="http://www.stubhub.com/detroit-lions-detroit-ford-field-16-12-2013-4270611?gcid=C12289x445&amp;keyword=NFL+Schedule+Detroit+Lions+20131216">14,874 available from $55</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/8">Ford Field</a></td>
</tr>
</tbody></table><table class="tablehead" cellpadding="3" cellspacing="1"><tbody><tr class="stathead"><td colspan="5"><a name="16" style="color:#FFFFFF;"></a>Week 16<span><a href="#top">back to top �</a></span></td></tr>
<tr class="colhead">
<td width="170">SUN, DEC 22</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-15 team-28-2"><td><a href="http://espn.go.com/nfl/team/_/name/mia/miami-dolphins">Miami</a> at <a href="http://espn.go.com/nfl/team/_/name/buf/buffalo-bills">Buffalo</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/buffalo-bills-orchard-park-ralph-wilson-stadium-22-12-2013-4271237?gcid=C12289x445&amp;keyword=NFL+Schedule+Buffalo+Bills+20131222">7,834 available from $22</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/2">Ralph Wilson Stadium</a></td>
</tr>
<tr class="evenrow team-28-16 team-28-4"><td><a href="http://espn.go.com/nfl/team/_/name/min/minnesota-vikings">Minnesota</a> at <a href="http://espn.go.com/nfl/team/_/name/cin/cincinnati-bengals">Cincinnati</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/cincinnati-bengals-cincinnati-paul-brown-stadium-22-12-2013-4271222?gcid=C12289x445&amp;keyword=NFL+Schedule+Cincinnati+Bengals+20131222">7,438 available from $35</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/4">Paul Brown Stadium</a></td>
</tr>
<tr class="oddrow team-28-11 team-28-12"><td><a href="http://espn.go.com/nfl/team/_/name/ind/indianapolis-colts">Indianapolis</a> at <a href="http://espn.go.com/nfl/team/_/name/kc/kansas-city-chiefs">Kansas City</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/kansas-city-chiefs-kansas-city-arrowhead-stadium-22-12-2013-4271200?gcid=C12289x445&amp;keyword=NFL+Schedule+Kansas+City+Chiefs+20131222">11,342 available from $21</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/12">Arrowhead Stadium</a></td>
</tr>
<tr class="evenrow team-28-27 team-28-14"><td><a href="http://espn.go.com/nfl/team/_/name/tb/tampa-bay-buccaneers">Tampa Bay</a> at <a href="http://espn.go.com/nfl/team/_/name/stl/st.-louis-rams">St. Louis</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/st-louis-rams-saint-louis-edward-jones-dome-22-12-2013-4271279?gcid=C12289x445&amp;keyword=NFL+Schedule+St.+Louis+Rams+20131222">7,929 available from $21</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/14">Edward Jones Dome</a></td>
</tr>
<tr class="oddrow team-28-5 team-28-20"><td><a href="http://espn.go.com/nfl/team/_/name/cle/cleveland-browns">Cleveland</a> at <a href="http://espn.go.com/nfl/team/_/name/nyj/new-york-jets">NY Jets</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/new-york-jets-east-rutherford-metlife-stadium-22-12-2013-4270605?gcid=C12289x445&amp;keyword=NFL+Schedule+New+York+Jets+20131222">10,187 available from $21</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/20">MetLife Stadium</a></td>
</tr>
<tr class="evenrow team-28-3 team-28-21"><td><a href="http://espn.go.com/nfl/team/_/name/chi/chicago-bears">Chicago</a> at <a href="http://espn.go.com/nfl/team/_/name/phi/philadelphia-eagles">Philadelphia</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/philadelphia-eagles-philadelphia-lincoln-financial-field-22-12-2013-4271210?gcid=C12289x445&amp;keyword=NFL+Schedule+Philadelphia+Eagles+20131222">6,536 available from $60</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/21">Lincoln Financial Field</a></td>
</tr>
<tr class="oddrow team-28-6 team-28-28"><td><a href="http://espn.go.com/nfl/team/_/name/dal/dallas-cowboys">Dallas</a> at <a href="http://espn.go.com/nfl/team/_/name/wsh/washington-redskins">Washington</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/washington-redskins-landover-fedexfield-22-12-2013-4271227?gcid=C12289x445&amp;keyword=NFL+Schedule+Washington+Redskins+20131222">9,793 available from $44</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/28">FedEx Field</a></td>
</tr>
<tr class="evenrow team-28-18 team-28-29"><td><a href="http://espn.go.com/nfl/team/_/name/no/new-orleans-saints">New Orleans</a> at <a href="http://espn.go.com/nfl/team/_/name/car/carolina-panthers">Carolina</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/carolina-panthers-charlotte-bank-of-america-stadium-22-12-2013-4270710?gcid=C12289x445&amp;keyword=NFL+Schedule+Carolina+Panthers+20131222">5,514 available from $43</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/29">Bank of America Stadium</a></td>
</tr>
<tr class="oddrow team-28-10 team-28-30"><td><a href="http://espn.go.com/nfl/team/_/name/ten/tennessee-titans">Tennessee</a> at <a href="http://espn.go.com/nfl/team/_/name/jac/jacksonville-jaguars">Jacksonville</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/jacksonville-jaguars-jacksonville-everbank-field-22-12-2013-4271258?gcid=C12289x445&amp;keyword=NFL+Schedule+Jacksonville+Jaguars+20131222">3,359 available from $29</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/30">EverBank Field</a></td>
</tr>
<tr class="evenrow team-28-7 team-28-34"><td><a href="http://espn.go.com/nfl/team/_/name/den/denver-broncos">Denver</a> at <a href="http://espn.go.com/nfl/team/_/name/hou/houston-texans">Houston</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/houston-texans-houston-reliant-stadium-22-12-2013-4271246?gcid=C12289x445&amp;keyword=NFL+Schedule+Houston+Texans+20131222">5,307 available from $39</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/34">Reliant Stadium</a></td>
</tr>
<tr class="oddrow team-28-22 team-28-26"><td><a href="http://espn.go.com/nfl/team/_/name/ari/arizona-cardinals">Arizona</a> at <a href="http://espn.go.com/nfl/team/_/name/sea/seattle-seahawks">Seattle</a></td>
<td>4:05 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/seattle-seahawks-seattle-centurylink-field-22-12-2013-4270614?gcid=C12289x445&amp;keyword=NFL+Schedule+Seattle+Seahawks+20131222">6,733 available from $87</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/26">CenturyLink Field</a></td>
</tr>
<tr class="evenrow team-28-19 team-28-8"><td><a href="http://espn.go.com/nfl/team/_/name/nyg/new-york-giants">NY Giants</a> at <a href="http://espn.go.com/nfl/team/_/name/det/detroit-lions">Detroit</a></td>
<td>4:05 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/detroit-lions-detroit-ford-field-22-12-2013-4270613?gcid=C12289x445&amp;keyword=NFL+Schedule+Detroit+Lions+20131222">16,272 available from $44</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/8">Ford Field</a></td>
</tr>
<tr class="oddrow team-28-23 team-28-9"><td><a href="http://espn.go.com/nfl/team/_/name/pit/pittsburgh-steelers">Pittsburgh</a> at <a href="http://espn.go.com/nfl/team/_/name/gb/green-bay-packers">Green Bay</a></td>
<td>4:25 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/green-bay-packers-green-bay-lambeau-field-22-12-2013-4271142?gcid=C12289x445&amp;keyword=NFL+Schedule+Green+Bay+Packers+20131222">4,514 available from $81</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/9">Lambeau Field</a></td>
</tr>
<tr class="evenrow team-28-13 team-28-24"><td><a href="http://espn.go.com/nfl/team/_/name/oak/oakland-raiders">Oakland</a> at <a href="http://espn.go.com/nfl/team/_/name/sd/san-diego-chargers">San Diego</a></td>
<td>4:25 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/san-diego-chargers-san-diego-qualcomm-stadium-22-12-2013-4270623?gcid=C12289x445&amp;keyword=NFL+Schedule+San+Diego+Chargers+20131222">10,474 available from $46</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/24">Qualcomm Stadium</a></td>
</tr>
<tr class="oddrow team-28-17 team-28-33"><td><a href="http://espn.go.com/nfl/team/_/name/ne/new-england-patriots">New England</a> at <a href="http://espn.go.com/nfl/team/_/name/bal/baltimore-ravens">Baltimore</a></td>
<td>8:30 PM</td>
<td align="center">NBC</td><td><a href="http://www.stubhub.com/baltimore-ravens-baltimore-m-t-bank-stadium-22-12-2013-4271203?gcid=C12289x445&amp;keyword=NFL+Schedule+Baltimore+Ravens+20131222">3,837 available from $139</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/33">M&amp;T Bank Stadium</a></td>
</tr>
<tr class="colhead">
<td width="170">MON, DEC 23</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-1 team-28-25"><td><a href="http://espn.go.com/nfl/team/_/name/atl/atlanta-falcons">Atlanta</a> at <a href="http://espn.go.com/nfl/team/_/name/sf/san-francisco-49ers">San Francisco</a></td>
<td>8:30 PM</td>
<td align="center"><a href="http://sports.espn.go.com/espntv/espnNetwork?networkID=1"><img src="http://a.espncdn.com/i/scoreboard/networkLogo_espn.gif" width="55" height="14"></a><a href="http://espn.go.com/watchespn/?channel=espn&amp;launchPlayer=true"><img src="http://a.espncdn.com/i/scoreboard/watchespn-55x12.png"></a></td><td><a href="http://www.stubhub.com/san-francisco-49ers-san-francisco-candlestick-park-23-12-2013-4270604?gcid=C12289x445&amp;keyword=NFL+Schedule+San+Francisco+49ers+20131223">13,231 available from $98</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/25">Candlestick Park</a></td>
</tr>
</tbody></table><table class="tablehead" cellpadding="3" cellspacing="1"><tbody><tr class="stathead"><td colspan="5"><a name="17" style="color:#FFFFFF;"></a>Week 17<span><a href="#top">back to top �</a></span></td></tr>
<tr class="colhead">
<td width="170">SUN, DEC 29</td>
<td width="80">TIME (ET)</td>
<td width="60" align="center">TV</td>
<td width="210">TICKETS</td>
<td width="220">LOCATION</td>
</tr>
<tr class="oddrow team-28-29 team-28-1"><td><a href="http://espn.go.com/nfl/team/_/name/car/carolina-panthers">Carolina</a> at <a href="http://espn.go.com/nfl/team/_/name/atl/atlanta-falcons">Atlanta</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/atlanta-falcons-atlanta-georgia-dome-29-12-2013-4271159?gcid=C12289x445&amp;keyword=NFL+Schedule+Atlanta+Falcons+20131229">9,130 available from $30</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/1">Georgia Dome</a></td>
</tr>
<tr class="evenrow team-28-9 team-28-3"><td><a href="http://espn.go.com/nfl/team/_/name/gb/green-bay-packers">Green Bay</a> at <a href="http://espn.go.com/nfl/team/_/name/chi/chicago-bears">Chicago</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/chicago-bears-chicago-soldier-field-29-12-2013-4271124?gcid=C12289x445&amp;keyword=NFL+Schedule+Chicago+Bears+20131229">6,762 available from $85</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/3">Soldier Field</a></td>
</tr>
<tr class="oddrow team-28-33 team-28-4"><td><a href="http://espn.go.com/nfl/team/_/name/bal/baltimore-ravens">Baltimore</a> at <a href="http://espn.go.com/nfl/team/_/name/cin/cincinnati-bengals">Cincinnati</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/cincinnati-bengals-cincinnati-paul-brown-stadium-29-12-2013-4270653?gcid=C12289x445&amp;keyword=NFL+Schedule+Cincinnati+Bengals+20131229">8,693 available from $35</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/4">Paul Brown Stadium</a></td>
</tr>
<tr class="evenrow team-28-21 team-28-6"><td><a href="http://espn.go.com/nfl/team/_/name/phi/philadelphia-eagles">Philadelphia</a> at <a href="http://espn.go.com/nfl/team/_/name/dal/dallas-cowboys">Dallas</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/dallas-cowboys-arlington-cowboys-stadium-29-12-2013-4271168?gcid=C12289x445&amp;keyword=NFL+Schedule+Dallas+Cowboys+20131229">17,540 available from $29</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/6">AT&amp;T Stadium</a></td>
</tr>
<tr class="oddrow team-28-34 team-28-10"><td><a href="http://espn.go.com/nfl/team/_/name/hou/houston-texans">Houston</a> at <a href="http://espn.go.com/nfl/team/_/name/ten/tennessee-titans">Tennessee</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/tennessee-titans-nashville-lp-field-29-12-2013-4270696?gcid=C12289x445&amp;keyword=NFL+Schedule+Tennessee+Titans+20131229">5,256 available from $30</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/10">LP Field</a></td>
</tr>
<tr class="evenrow team-28-30 team-28-11"><td><a href="http://espn.go.com/nfl/team/_/name/jac/jacksonville-jaguars">Jacksonville</a> at <a href="http://espn.go.com/nfl/team/_/name/ind/indianapolis-colts">Indianapolis</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/indianapolis-colts-indianapolis-lucas-oil-stadium-29-12-2013-4271262?gcid=C12289x445&amp;keyword=NFL+Schedule+Indianapolis+Colts+20131229">5,693 available from $26</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/11">Lucas Oil Stadium</a></td>
</tr>
<tr class="oddrow team-28-5 team-28-23"><td><a href="http://espn.go.com/nfl/team/_/name/cle/cleveland-browns">Cleveland</a> at <a href="http://espn.go.com/nfl/team/_/name/pit/pittsburgh-steelers">Pittsburgh</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/pittsburgh-steelers-pittsburgh-heinz-field-29-12-2013-4271247?gcid=C12289x445&amp;keyword=NFL+Schedule+Pittsburgh+Steelers+20131229">5,330 available from $58</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/23">Heinz Field</a></td>
</tr>
<tr class="evenrow team-28-20 team-28-15"><td><a href="http://espn.go.com/nfl/team/_/name/nyj/new-york-jets">NY Jets</a> at <a href="http://espn.go.com/nfl/team/_/name/mia/miami-dolphins">Miami</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/miami-dolphins-miami-gardens-sun-life-stadium-29-12-2013-4271170?gcid=C12289x445&amp;keyword=NFL+Schedule+Miami+Dolphins+20131229">6,154 available from $37</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/15">Sun Life Stadium</a></td>
</tr>
<tr class="oddrow team-28-8 team-28-16"><td><a href="http://espn.go.com/nfl/team/_/name/det/detroit-lions">Detroit</a> at <a href="http://espn.go.com/nfl/team/_/name/min/minnesota-vikings">Minnesota</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/minnesota-vikings-minneapolis-hubert-h--humphrey-metrodome-29-12-2013-4271155?gcid=C12289x445&amp;keyword=NFL+Schedule+Minnesota+Vikings+20131229">8,407 available from $44</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/16">Mall of America Field</a></td>
</tr>
<tr class="evenrow team-28-2 team-28-17"><td><a href="http://espn.go.com/nfl/team/_/name/buf/buffalo-bills">Buffalo</a> at <a href="http://espn.go.com/nfl/team/_/name/ne/new-england-patriots">New England</a></td>
<td>1:00 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/new-england-patriots-foxborough-gillette-stadium-29-12-2013-4270633?gcid=C12289x445&amp;keyword=NFL+Schedule+New+England+Patriots+20131229">3,299 available from $107</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/17">Gillette Stadium</a></td>
</tr>
<tr class="oddrow team-28-27 team-28-18"><td><a href="http://espn.go.com/nfl/team/_/name/tb/tampa-bay-buccaneers">Tampa Bay</a> at <a href="http://espn.go.com/nfl/team/_/name/no/new-orleans-saints">New Orleans</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/new-orleans-saints-new-orleans-mercedes-benz-superdome-29-12-2013-4270651?gcid=C12289x445&amp;keyword=NFL+Schedule+New+Orleans+Saints+20131229">3,900 available from $59</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/18">Mercedes-Benz Superdome</a></td>
</tr>
<tr class="evenrow team-28-28 team-28-19"><td><a href="http://espn.go.com/nfl/team/_/name/wsh/washington-redskins">Washington</a> at <a href="http://espn.go.com/nfl/team/_/name/nyg/new-york-giants">NY Giants</a></td>
<td>1:00 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/new-york-giants-east-rutherford-metlife-stadium-29-12-2013-4271183?gcid=C12289x445&amp;keyword=NFL+Schedule+New+York+Giants+20131229">7,448 available from $46</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/19">MetLife Stadium</a></td>
</tr>
<tr class="oddrow team-28-25 team-28-22"><td><a href="http://espn.go.com/nfl/team/_/name/sf/san-francisco-49ers">San Francisco</a> at <a href="http://espn.go.com/nfl/team/_/name/ari/arizona-cardinals">Arizona</a></td>
<td>4:25 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/arizona-cardinals-glendale-university-of-phoenix-stadium-29-12-2013-4271235?gcid=C12289x445&amp;keyword=NFL+Schedule+Arizona+Cardinals+20131229">9,204 available from $5</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/22">U of Phoenix Stadium</a></td>
</tr>
<tr class="evenrow team-28-12 team-28-24"><td><a href="http://espn.go.com/nfl/team/_/name/kc/kansas-city-chiefs">Kansas City</a> at <a href="http://espn.go.com/nfl/team/_/name/sd/san-diego-chargers">San Diego</a></td>
<td>4:25 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/san-diego-chargers-san-diego-qualcomm-stadium-29-12-2013-4270626?gcid=C12289x445&amp;keyword=NFL+Schedule+San+Diego+Chargers+20131229">10,238 available from $37</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/24">Qualcomm Stadium</a></td>
</tr>
<tr class="oddrow team-28-14 team-28-26"><td><a href="http://espn.go.com/nfl/team/_/name/stl/st.-louis-rams">St. Louis</a> at <a href="http://espn.go.com/nfl/team/_/name/sea/seattle-seahawks">Seattle</a></td>
<td>4:25 PM</td>
<td align="center">FOX</td><td><a href="http://www.stubhub.com/seattle-seahawks-seattle-centurylink-field-29-12-2013-4270616?gcid=C12289x445&amp;keyword=NFL+Schedule+Seattle+Seahawks+20131229">6,671 available from $87</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/26">CenturyLink Field</a></td>
</tr>
<tr class="evenrow team-28-7 team-28-13"><td><a href="http://espn.go.com/nfl/team/_/name/den/denver-broncos">Denver</a> at <a href="http://espn.go.com/nfl/team/_/name/oak/oakland-raiders">Oakland</a></td>
<td>4:25 PM</td>
<td align="center">CBS</td><td><a href="http://www.stubhub.com/oakland-raiders-oakland-o-co-coliseum-29-12-2013-4271164?gcid=C12289x445&amp;keyword=NFL+Schedule+Oakland+Raiders+20131229">8,085 available from $38</a></td>
<td><a href="http://espn.go.com/travel/stadium/_/s/nfl/id/13">O.co Coliseum</a></td>
</tr>
</tbody></table>


</div>