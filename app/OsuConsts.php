<?php
namespace App;

class OsuConsts {
  public const Nomod = 0;
	public const NoFail = 1 << 0;
	public const Easy = 1 << 1;
	public const TouchDevice = 1 << 2;
	public const Hidden = 1 << 3;
	public const HardRock = 1 << 4;
	public const SuddenDeath = 1 << 5;
	public const DoubleTime = 1 << 6;
	public const Relax = 1 << 7;
	public const HalfTime = 1 << 8;
	public const Nightcore = 1 << 9;
	public const Flashlight = 1 << 10;
	public const Autoplay = 1 << 11;
	public const SpunOut = 1 << 12;
	public const Relax2 = 1 << 13;
	public const Perfect = 1 << 14;
	public const Key4 = 1 << 15;
	public const Key5 = 1 << 16;
	public const Key6 = 1 << 17;
	public const Key7 = 1 << 18;
	public const Key8 = 1 << 19;
	public const FadeIn = 1 << 20;
	public const Random = 1 << 21;
	public const Cinema = 1 << 22;
	public const Target = 1 << 23;
	public const Key9 = 1 << 24;
	public const Key10 = 1 << 25;
	public const Key1 = 1 << 26;
	public const Key3 = 1 << 27;
	public const Key2 = 1 << 28;
	public const LastMod = 1 << 29;
	public const keyMod = Key1 | Key2 | Key3 | Key4 | Key5 | Key6 | Key7 | Key8 | Key9 | Key10;
	public const KeyModUnranked = Key1 | Key2 | Key3 | Key9 | Key10;
	public const FreeModAllowed = NoFail | Easy | Hidden | HardRock | SuddenDeath | Flashlight | FadeIn | Relax | Relax2 | SpunOut | keyMod;
	public const ScoreIncreaseMods = Hidden | HardRock | DoubleTime | Flashlight | FadeIn;
}
?>
