<CsoundSynthesizer>
<CsOptions>
-odac -d
</CsOptions>
<CsInstruments>

sr = 44100
ksmps = 32
nchnls = 2
0dbfs = 1

#define SERVER #"192.168.0.199"#
#define PORT #5000#
#define CYCLELEN #40# ; after how many beats, send "LONGBEAT"

giSine ftgen 1, 0, 4096, 10, 1

chn_k "beats", 3
chn_k "bardur", 3
chn_k "beatrand",3
chn_k "subdiv", 3
chn_k "subdivrand",3
chn_k "generate", 1
chn_k "pan",3
chn_k "volume",3

seed 0

alwayson "master"

gkMeasure init 0
gkPan init 0.5
gkVolume init 0.8
gkBeatCount init 0

instr master
	gkMeasure chnget "generate"
	gkPan chnget "pan"
	gkVolume chnget "volume"
	;printk2 gkPan
	kMChanged changed gkMeasure
	if (kMChanged == 1) then 
		if (gkMeasure == 1) then
			event "i", "measure",0,-1
		elseif (gkMeasure == 0) then
			event "i",-2, 0, 0 ; TODO: find out instr no
		endif
	endif
endin
		
instr measure
	kBeats chnget "beats"
	kBarDur chnget "bardur"
	kBeatRand chnget "beatrand"
	kBeatDev init 1 
	
	kBeatDur=kBeatDev*kBarDur/kBeats
	kTrig metro 1/kBeatDur
	
	if (kTrig==1) then
		gkBeatCount = gkBeatCount + 1
		event "i","beat", 0, kBeatDur
		kBeatRand chnget "beatrand"
		kBeatDev = 1+birnd(kBeatRand); vahemikus 0.5..1.5
	endif
	
endin


instr beat ; play beat and subdivisions
	kSDCount init 0
	kSDDev init 1
	event_i "i", "sendBeat", 0,0.1
	
		
	if (gkBeatCount % $CYCLELEN)== 0 then
		
		if ( (int(gkBeatCount/ ($CYCLELEN*4))% 2) == 0 ) then ; after every N cycles, change, which prolonged sound is to be played

			OSCsend gkBeatCount, $SERVER, $PORT, "/drums/longbeat", "i", 50 	
		else
			OSCsend gkBeatCount, $SERVER, $PORT, "/drums/longsubdiv", "i", 40
		endif	
	endif
	
	

	kSubDivs chnget "subdiv" ; kontrolli, mitmeks jagada ainult löögil
	if (kSubDivs > 0) then
		kSDDur=kSDDev*p3/(kSubDivs+1)
		kSDTrig metro 1/kSDDur
		if (kSDTrig == 1) then 
			if (kSDCount>0 && kSDCount<=kSubDivs ) then
				printk2 kSDCount
				event "i", "sendSubDiv", 0, 0.1
				 
	
			endif
			kSDCount = kSDCount + 1
			if (kSDCount==(kSubDivs+1)) then
				kSDCount = 0
			endif
			kSDrand chnget "subdivrand"
			kSDDev = 1+birnd(kSDrand); kõrvalekalle, vahemikus 0.5..1.5
		endif
		
	endif
endin

instr sendBeat	
	OSCsend 1, $SERVER, $PORT, "/drums/beat", "ff",gkPan, gkVolume 
	event_i "i", "rand_tom", 0, 0, 1	
endin

instr sendSubDiv
	OSCsend 1, $SERVER, $PORT, "/drums/subdiv", "ff",gkPan, gkVolume
	event_i "i", "rand_snare", 0, 0, 1
endin

instr rand_tom
	iamp random 0.2, 0.5 ; hiljem 1 ;= ampdbfs(p4) ; in dbfs
	;TODO: hiljem parameetrist
	iamp = iamp*i(gkVolume)
	idurscale = (p4==0) ? 1 : p4 ; kui määratlemata, siis pane 1
	ifreq random 40,80
	idur random 0.2*idurscale,0.5*idurscale
	irez random 1,100
	ifco random 400,3000
	ihit=idur*(1-rnd(0.5))
	ihamp random 20,80
	;ipan random 0.2,0.8
	ipan = i(gkPan)
	;print idur,iamp,ifreq,irez,ifco,ihit,ihamp;,ipan
	event_i "i","tomtom", 0, idur, iamp,ifreq, irez,ifco,ihit,ihamp,ipan
endin


   
; FM Tom-Tom
;---------------------------------------------------------
       instr     tomtom
; From Hans Mikelson

idur   =         p3            ; Duration
; norm 0.13-0.2, noise dur veidi pikem või sama
; pikk pikkus nagu 60 lahe
iamp   =         p4            ; Amplitude
ifqc   =        p5 ; Hertsides ;cpspch(p5)    ; Convert pitch to frequency
; mõnusamad on madala 6.00 jms
irez   =         p6            ; Resonance or Q
; kui suurem (üle 30), siis attakk rohkem heli, kui väikse, (kuni 1), siis rohkem müra; peaks olema vahemikus 1..100 (amount of resonancs in rezzy
ifco   =         p7            ; Cut off frequency
; kui on suht madalam, rohkem müra signaalis
ihit   =         p8            ; Noise duration
ihamp  =         p9            ; Noise amplitude
ipan= p10

afqc1  linseg    1+iamp, ihit*.5*idur, 1, .1, 1 ; Pitch bend ; /30000
afqc   =         afqc1*afqc1                       ; Pitch bend squared
adclck linen 1,0.002,p3,0.05;linseg    0, .002, 1, idur-.004, 1, .01, 0 ; Declick envelope
aamp1  expseg    .01, .001, 1, idur-.001, .04      ; Tone envelope
aamp2  expseg    .01, .001, 1, idur*ihit-.001, .01 ; Noise envelope

arnd1  rand      ihamp                          ; Genrate noise
arnd   rezzy     arnd1, ifco, irez, 1           ; High pass mode
asig   oscil     1, afqc*ifqc*(1+arnd*aamp2), 1 ; Frequency modulation with noise

aout   =         asig*iamp*aamp1*adclck         ; Apply amp envelope and declick

       aL, aR pan2 aout, ipan
       outs      aL, aR         ; Output the sound
        endin

instr rand_snare
	iamp random 0.05, 0.1 ; hiljem 1 ;= ampdbfs(p4) ; in dbf
	;TODO: hiljem parameetrist
	iamp = iamp*i(gkVolume)
	idurscale = (p4==0) ? 1 : p4 ; kui määratlemata, siis pane 1
	idur  random 0.3,1
	idur = idur*idurscale
	;ifqc random 100,110
	ifqc random 120,140
	icutfr random 3000,6000
	iband random 2000,6000
	ipan	= i(gkPan)	
	event_i "i", "snare", 0, idur, iamp,ifqc,icutfr,iband,ipan
endin
      
       
; -- SIMPLE SNARE ------------------------

instr snare; snare - päris hea, testi pikkusega ja pikendamisega!
  icps0  = p5;147
  iamp   = p4
  ipan = p8
  idur = p3
  icutfr = p6
  iband = p7

  icps1  =  2.0 * icps0
  
  kcps   port icps0, idur/7, icps1 ; idur/14
  kcpsx  =  kcps * 1.5  
  kfmd   port   0.0, idur/50, 0.7 ; idur/100
  aenv1  expon  1.0, idur/15, 0.5 ; idur/33
  kenv2  port   1.0, idur/60, 0.0 ; idur/125
  aenv2  interp kenv2
  aenv3  expon  1.0, idur/20, 0.5 ; idur/40
  
  a_     oscili 1.0, kcps, 1
  a1     oscili 1.0, kcps * (1.0 + a_*kfmd), 1
  a_     oscili 1.0, kcpsx, 1
  a2     oscili 1.0, kcpsx * (1.0 + a_*kfmd), 1
  
  a3     unirand 2.0
  a3     =  a3 - 1.0
  a3     butterbp a3, icutfr, iband
  a3     =  a3 * aenv2
  
  a0     =  a1 + a2*aenv3 + a3*1.0
  a0     =  a0 * aenv1

  aL,aR pan2 a0*iamp,ipan
  outs aL,aR
  
endin


</CsInstruments>
<CsScore>

</CsScore>
</CsoundSynthesizer>


<CsApp>
    <appName>drum-client</appName>
    <targetDir>/home/tarmo/tarmo/csound/drums</targetDir>
    <author></author>
    <version></version>
    <date>September 21 2012</date>
    <email></email>
    <website></website>
    <instructions></instructions>
    <autorun>true</autorun>
    <showRun>true</showRun>
    <saveState>true</saveState>
    <runMode>0</runMode>
    <newParser>true</newParser>
    <useSdk>false</useSdk>
    <useCustomPaths>false</useCustomPaths>
    <libDir>/usr/local/lib</libDir>
    <opcodeDir>/usr/local/lib/csound/plugins64</opcodeDir>
</CsApp>
<bsbPanel>
 <label>Widgets</label>
 <objectName/>
 <x>708</x>
 <y>63</y>
 <width>273</width>
 <height>438</height>
 <visible>true</visible>
 <uuid/>
 <bgcolor mode="nobackground">
  <r>231</r>
  <g>46</g>
  <b>255</b>
 </bgcolor>
 <bsbObject version="2" type="BSBHSlider">
  <objectName>bardur</objectName>
  <x>117</x>
  <y>77</y>
  <width>119</width>
  <height>28</height>
  <uuid>{47afd380-f022-4538-8620-452b1482b85c}</uuid>
  <visible>true</visible>
  <midichan>0</midichan>
  <midicc>0</midicc>
  <minimum>3.00000000</minimum>
  <maximum>7.00000000</maximum>
  <value>4.17647059</value>
  <mode>lin</mode>
  <mouseControl act="jump">continuous</mouseControl>
  <resolution>-1.00000000</resolution>
  <randomizable group="0">false</randomizable>
 </bsbObject>
 <bsbObject version="2" type="BSBButton">
  <objectName>sendBeat</objectName>
  <x>14</x>
  <y>374</y>
  <width>100</width>
  <height>30</height>
  <uuid>{e8d89c25-f458-4a79-8f06-33fbdcbc73d2}</uuid>
  <visible>true</visible>
  <midichan>0</midichan>
  <midicc>0</midicc>
  <type>event</type>
  <pressedValue>1.00000000</pressedValue>
  <stringvalue/>
  <text>Beat</text>
  <image>/</image>
  <eventLine>i "sendBeat" 0 0.1</eventLine>
  <latch>false</latch>
  <latched>true</latched>
 </bsbObject>
 <bsbObject version="2" type="BSBSpinBox">
  <objectName>beats</objectName>
  <x>120</x>
  <y>45</y>
  <width>38</width>
  <height>25</height>
  <uuid>{d2f73c49-88c7-4b93-b087-c45e7afdf311}</uuid>
  <visible>true</visible>
  <midichan>0</midichan>
  <midicc>0</midicc>
  <alignment>left</alignment>
  <font>Liberation Sans</font>
  <fontsize>10</fontsize>
  <color>
   <r>0</r>
   <g>0</g>
   <b>0</b>
  </color>
  <bgcolor mode="nobackground">
   <r>255</r>
   <g>255</g>
   <b>255</b>
  </bgcolor>
  <resolution>1.00000000</resolution>
  <minimum>2</minimum>
  <maximum>5</maximum>
  <randomizable group="0">false</randomizable>
  <value>4</value>
 </bsbObject>
 <bsbObject version="2" type="BSBLabel">
  <objectName/>
  <x>12</x>
  <y>48</y>
  <width>103</width>
  <height>25</height>
  <uuid>{6522d7ed-6509-45d1-85cd-74edc21ba578}</uuid>
  <visible>true</visible>
  <midichan>0</midichan>
  <midicc>0</midicc>
  <label>Beats in measure:</label>
  <alignment>left</alignment>
  <font>Liberation Sans</font>
  <fontsize>10</fontsize>
  <precision>3</precision>
  <color>
   <r>0</r>
   <g>0</g>
   <b>0</b>
  </color>
  <bgcolor mode="nobackground">
   <r>255</r>
   <g>255</g>
   <b>255</b>
  </bgcolor>
  <bordermode>noborder</bordermode>
  <borderradius>1</borderradius>
  <borderwidth>1</borderwidth>
 </bsbObject>
 <bsbObject version="2" type="BSBLabel">
  <objectName/>
  <x>12</x>
  <y>74</y>
  <width>103</width>
  <height>34</height>
  <uuid>{563dae41-1649-4634-b088-d1c9f5bf5918}</uuid>
  <visible>true</visible>
  <midichan>0</midichan>
  <midicc>0</midicc>
  <label>Lengths of measure (s):</label>
  <alignment>left</alignment>
  <font>Liberation Sans</font>
  <fontsize>10</fontsize>
  <precision>3</precision>
  <color>
   <r>0</r>
   <g>0</g>
   <b>0</b>
  </color>
  <bgcolor mode="nobackground">
   <r>255</r>
   <g>255</g>
   <b>255</b>
  </bgcolor>
  <bordermode>noborder</bordermode>
  <borderradius>1</borderradius>
  <borderwidth>1</borderwidth>
 </bsbObject>
 <bsbObject version="2" type="BSBHSlider">
  <objectName>beatrand</objectName>
  <x>121</x>
  <y>113</y>
  <width>119</width>
  <height>25</height>
  <uuid>{bfeb390b-baf5-4aff-a49a-950b76dcbdcc}</uuid>
  <visible>true</visible>
  <midichan>0</midichan>
  <midicc>0</midicc>
  <minimum>0.00000000</minimum>
  <maximum>0.50000000</maximum>
  <value>0.00000000</value>
  <mode>lin</mode>
  <mouseControl act="jump">continuous</mouseControl>
  <resolution>-1.00000000</resolution>
  <randomizable group="0">false</randomizable>
 </bsbObject>
 <bsbObject version="2" type="BSBLabel">
  <objectName/>
  <x>12</x>
  <y>108</y>
  <width>104</width>
  <height>38</height>
  <uuid>{7979573a-e1f2-4510-aea9-e65100e3b42a}</uuid>
  <visible>true</visible>
  <midichan>0</midichan>
  <midicc>0</midicc>
  <label>Allowed deviation of beat (0..0.5)</label>
  <alignment>left</alignment>
  <font>Liberation Sans</font>
  <fontsize>10</fontsize>
  <precision>3</precision>
  <color>
   <r>0</r>
   <g>0</g>
   <b>0</b>
  </color>
  <bgcolor mode="nobackground">
   <r>255</r>
   <g>255</g>
   <b>255</b>
  </bgcolor>
  <bordermode>noborder</bordermode>
  <borderradius>1</borderradius>
  <borderwidth>1</borderwidth>
 </bsbObject>
 <bsbObject version="2" type="BSBButton">
  <objectName>button7</objectName>
  <x>13</x>
  <y>408</y>
  <width>100</width>
  <height>30</height>
  <uuid>{f956e23b-e4a4-47e8-8456-8370f32b8b46}</uuid>
  <visible>true</visible>
  <midichan>0</midichan>
  <midicc>0</midicc>
  <type>event</type>
  <pressedValue>1.00000000</pressedValue>
  <stringvalue/>
  <text>SubDiv</text>
  <image>/</image>
  <eventLine>i "sendSubDiv" 0 0.1</eventLine>
  <latch>false</latch>
  <latched>true</latched>
 </bsbObject>
 <bsbObject version="2" type="BSBSpinBox">
  <objectName>subdiv</objectName>
  <x>126</x>
  <y>162</y>
  <width>38</width>
  <height>25</height>
  <uuid>{e738ad9e-8dbb-409d-be30-6b3dd7fa6171}</uuid>
  <visible>true</visible>
  <midichan>0</midichan>
  <midicc>0</midicc>
  <alignment>left</alignment>
  <font>Liberation Sans</font>
  <fontsize>10</fontsize>
  <color>
   <r>0</r>
   <g>0</g>
   <b>0</b>
  </color>
  <bgcolor mode="nobackground">
   <r>255</r>
   <g>255</g>
   <b>255</b>
  </bgcolor>
  <resolution>1.00000000</resolution>
  <minimum>0</minimum>
  <maximum>3</maximum>
  <randomizable group="0">false</randomizable>
  <value>2</value>
 </bsbObject>
 <bsbObject version="2" type="BSBLabel">
  <objectName/>
  <x>12</x>
  <y>161</y>
  <width>112</width>
  <height>28</height>
  <uuid>{27acc88d-09ef-4760-b5e7-6002f690c7ab}</uuid>
  <visible>true</visible>
  <midichan>0</midichan>
  <midicc>0</midicc>
  <label>Subdivisons in beat:</label>
  <alignment>left</alignment>
  <font>Liberation Sans</font>
  <fontsize>10</fontsize>
  <precision>3</precision>
  <color>
   <r>0</r>
   <g>0</g>
   <b>0</b>
  </color>
  <bgcolor mode="nobackground">
   <r>255</r>
   <g>255</g>
   <b>255</b>
  </bgcolor>
  <bordermode>noborder</bordermode>
  <borderradius>1</borderradius>
  <borderwidth>1</borderwidth>
 </bsbObject>
 <bsbObject version="2" type="BSBHSlider">
  <objectName>subdivrand</objectName>
  <x>121</x>
  <y>196</y>
  <width>119</width>
  <height>25</height>
  <uuid>{d434ac96-f001-40d0-af49-6ecb38903702}</uuid>
  <visible>true</visible>
  <midichan>0</midichan>
  <midicc>0</midicc>
  <minimum>0.00000000</minimum>
  <maximum>0.25000000</maximum>
  <value>0.00000000</value>
  <mode>lin</mode>
  <mouseControl act="jump">continuous</mouseControl>
  <resolution>-1.00000000</resolution>
  <randomizable group="0">false</randomizable>
 </bsbObject>
 <bsbObject version="2" type="BSBLabel">
  <objectName/>
  <x>12</x>
  <y>191</y>
  <width>106</width>
  <height>40</height>
  <uuid>{71dd2ea3-cf1c-4192-ae99-24b64663013f}</uuid>
  <visible>true</visible>
  <midichan>0</midichan>
  <midicc>0</midicc>
  <label>Allowed deviation (0..0.5)</label>
  <alignment>left</alignment>
  <font>Liberation Sans</font>
  <fontsize>10</fontsize>
  <precision>3</precision>
  <color>
   <r>0</r>
   <g>0</g>
   <b>0</b>
  </color>
  <bgcolor mode="nobackground">
   <r>255</r>
   <g>255</g>
   <b>255</b>
  </bgcolor>
  <bordermode>noborder</bordermode>
  <borderradius>1</borderradius>
  <borderwidth>1</borderwidth>
 </bsbObject>
 <bsbObject version="2" type="BSBCheckBox">
  <objectName>generate</objectName>
  <x>15</x>
  <y>13</y>
  <width>20</width>
  <height>20</height>
  <uuid>{e6f7391d-e938-413f-bf10-212493e27c10}</uuid>
  <visible>true</visible>
  <midichan>0</midichan>
  <midicc>0</midicc>
  <selected>true</selected>
  <label/>
  <pressedValue>1</pressedValue>
  <randomizable group="0">false</randomizable>
 </bsbObject>
 <bsbObject version="2" type="BSBLabel">
  <objectName/>
  <x>12</x>
  <y>347</y>
  <width>80</width>
  <height>25</height>
  <uuid>{de2847cd-f284-4e9a-91c2-f79635e94074}</uuid>
  <visible>true</visible>
  <midichan>0</midichan>
  <midicc>0</midicc>
  <label>Play here:</label>
  <alignment>left</alignment>
  <font>Liberation Sans</font>
  <fontsize>10</fontsize>
  <precision>3</precision>
  <color>
   <r>0</r>
   <g>0</g>
   <b>0</b>
  </color>
  <bgcolor mode="nobackground">
   <r>255</r>
   <g>255</g>
   <b>255</b>
  </bgcolor>
  <bordermode>noborder</bordermode>
  <borderradius>1</borderradius>
  <borderwidth>1</borderwidth>
 </bsbObject>
 <bsbObject version="2" type="BSBLabel">
  <objectName/>
  <x>44</x>
  <y>10</y>
  <width>80</width>
  <height>25</height>
  <uuid>{8bba8d99-d858-4c2a-bdf6-c4f34baf65b9}</uuid>
  <visible>true</visible>
  <midichan>0</midichan>
  <midicc>0</midicc>
  <label>Generate</label>
  <alignment>left</alignment>
  <font>Liberation Sans</font>
  <fontsize>10</fontsize>
  <precision>3</precision>
  <color>
   <r>0</r>
   <g>0</g>
   <b>0</b>
  </color>
  <bgcolor mode="nobackground">
   <r>255</r>
   <g>255</g>
   <b>255</b>
  </bgcolor>
  <bordermode>noborder</bordermode>
  <borderradius>1</borderradius>
  <borderwidth>1</borderwidth>
 </bsbObject>
 <bsbObject version="2" type="BSBHSlider">
  <objectName>pan</objectName>
  <x>156</x>
  <y>245</y>
  <width>101</width>
  <height>25</height>
  <uuid>{ff266345-344d-4f2b-b12a-b3908d5bc527}</uuid>
  <visible>true</visible>
  <midichan>0</midichan>
  <midicc>0</midicc>
  <minimum>0.00000000</minimum>
  <maximum>1.00000000</maximum>
  <value>0.58415842</value>
  <mode>lin</mode>
  <mouseControl act="jump">continuous</mouseControl>
  <resolution>-1.00000000</resolution>
  <randomizable group="0">false</randomizable>
 </bsbObject>
 <bsbObject version="2" type="BSBLabel">
  <objectName/>
  <x>193</x>
  <y>269</y>
  <width>80</width>
  <height>25</height>
  <uuid>{96b6e259-3158-4712-bb78-e73f073b2efc}</uuid>
  <visible>true</visible>
  <midichan>0</midichan>
  <midicc>0</midicc>
  <label>Pan</label>
  <alignment>left</alignment>
  <font>Liberation Sans</font>
  <fontsize>10</fontsize>
  <precision>3</precision>
  <color>
   <r>0</r>
   <g>0</g>
   <b>0</b>
  </color>
  <bgcolor mode="nobackground">
   <r>255</r>
   <g>255</g>
   <b>255</b>
  </bgcolor>
  <bordermode>noborder</bordermode>
  <borderradius>1</borderradius>
  <borderwidth>1</borderwidth>
 </bsbObject>
 <bsbObject version="2" type="BSBKnob">
  <objectName>volume</objectName>
  <x>41</x>
  <y>243</y>
  <width>62</width>
  <height>55</height>
  <uuid>{29065eed-1a10-4039-847a-82b897b7da6a}</uuid>
  <visible>true</visible>
  <midichan>0</midichan>
  <midicc>0</midicc>
  <minimum>0.00000000</minimum>
  <maximum>1.00000000</maximum>
  <value>0.30000000</value>
  <mode>lin</mode>
  <mouseControl act="jump">continuous</mouseControl>
  <resolution>0.01000000</resolution>
  <randomizable group="0">false</randomizable>
 </bsbObject>
 <bsbObject version="2" type="BSBLabel">
  <objectName/>
  <x>34</x>
  <y>303</y>
  <width>80</width>
  <height>25</height>
  <uuid>{32b30c33-6a1a-4957-8a6c-d794b8d9e788}</uuid>
  <visible>true</visible>
  <midichan>0</midichan>
  <midicc>0</midicc>
  <label>Volume</label>
  <alignment>left</alignment>
  <font>Liberation Sans</font>
  <fontsize>10</fontsize>
  <precision>3</precision>
  <color>
   <r>0</r>
   <g>0</g>
   <b>0</b>
  </color>
  <bgcolor mode="nobackground">
   <r>255</r>
   <g>255</g>
   <b>255</b>
  </bgcolor>
  <bordermode>noborder</bordermode>
  <borderradius>1</borderradius>
  <borderwidth>1</borderwidth>
 </bsbObject>
</bsbPanel>
<bsbPresets>
</bsbPresets>
