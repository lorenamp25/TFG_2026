import { ComponentFixture, TestBed } from '@angular/core/testing';

import { IngredienteTabla } from './ingrediente-tabla';

describe('IngredienteTabla', () => {
  let component: IngredienteTabla;
  let fixture: ComponentFixture<IngredienteTabla>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [IngredienteTabla]
    })
    .compileComponents();

    fixture = TestBed.createComponent(IngredienteTabla);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
