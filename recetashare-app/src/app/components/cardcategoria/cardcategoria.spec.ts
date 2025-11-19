import { ComponentFixture, TestBed } from '@angular/core/testing';

import { Cardcategoria } from './cardcategoria';

describe('Cardcategoria', () => {
  let component: Cardcategoria;
  let fixture: ComponentFixture<Cardcategoria>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [Cardcategoria]
    })
    .compileComponents();

    fixture = TestBed.createComponent(Cardcategoria);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
